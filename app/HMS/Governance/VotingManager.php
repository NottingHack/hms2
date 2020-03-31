<?php

namespace HMS\Governance;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\Repositories\Governance\ProxyRepository;
use HMS\Repositories\Governance\MeetingRepository;

class VotingManager
{
    const VOTING_MEMBER = 'Voting Member';
    const NONVOTING_MEMBER = 'Non-voting Member';
    const CANNOT_VOTE = 'Cannot vote';

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * @var MeetingRepository
     */
    protected $meetingRepository;

    /**
     * @var ProxyRepository
     */
    protected $proxyRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * VotingManager constructor.
     *
     * @param RoleRepository $roleRepository
     * @param MetaRepository $metaRepository
     * @param MeetingRepository $meetingRepository
     * @param ProxyRepository $proxyRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        RoleRepository $roleRepository,
        MetaRepository $metaRepository,
        MeetingRepository $meetingRepository,
        ProxyRepository $proxyRepository,
        UserRepository $userRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->metaRepository = $metaRepository;
        $this->meetingRepository = $meetingRepository;
        $this->proxyRepository = $proxyRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Count the number of Role::MEMBER_CURRENT Users.
     *
     * @return int
     */
    public function countCurrentMembers()
    {
        return $this->userRepository->countCurrentMembers();
    }

    /**
     * Find all Current and Voting Members.
     *
     * @return User[]|Illuminate\Support\Collection
     */
    public function votingMembers()
    {
        $v = collect();

        $statedVoting = $this->userRepository->findVotingStated();
        $statedNonVoting = collect($this->userRepository->findNonVotingStated());
        $physical = collect($this->userRepository->findVotingPhysical());

        $meetings = $this->meetingRepository->findPastSixMonths();
        $attendees = collect();
        $principals = collect();
        $absentees = collect();

        foreach ($meetings as $meeting) {
            $attendees = $attendees->merge($meeting->getAttendees())->unique();

            foreach ($meeting->getProxies() as $proxy) {
                $principal = $proxy->getPrincipal();
                if (! $principals->contains($principal)) {
                    $principals->add($principal);
                }
            }

            $absentees = $absentees->merge($meeting->getAbsentees())->unique();
        }

        $v = $v->merge($statedVoting)->unique();
        $v = $v->merge($attendees)->unique();
        $v = $v->merge($principals)->unique();
        $v = $v->merge($absentees)->unique();

        $physicalFiltered = $physical->reject(function ($user) use ($statedNonVoting) {
            return $statedNonVoting->contains($user);
        });
        $v = $v->merge($physicalFiltered)->unique();

        return $v;
    }

    /**
     * Count the number of Voting Members.
     *
     * @return int
     */
    public function countVotingMembers()
    {
        return $this->votingMembers()->count();
    }

    /**
     * Count the number of Non Voting Members.
     *
     * @return int
     */
    public function countNonVotingMembers()
    {
        return $this->countCurrentMembers() - $this->countVotingMembers(); // Fake it for now
    }

    /**
     * Calculate the current number of attendees requires to be quorate.
     *
     * @return int
     */
    public function currentQuorumRequirement()
    {
        $quorumPercent = $this->metaRepository->get('quorum_percent', 20);
        $votingMembers = $this->countVotingMembers();

        return ceil($votingMembers * ($quorumPercent / 100));
    }

    /**
     * Get Voting/NonVoting Status For a given User.
     *
     * @param User $user
     *
     * @return string
     */
    public function getVotingStatusForUser(User $user)
    {
        // Likely not a current member
        if ($user->cannot('governance.voting.canVote')) {
            return self::CANNOT_VOTE;
        }

        $sixMonthsAgo = Carbon::now()->subMonthsNoOverflow(6);

        // Has stated a preference for VOTING
        if ($user->getProfile()->getVotingPreference() == VotingPreference::VOTING && $user->getProfile()->getVotingPreferenceStatedAt()->isAfter($sixMonthsAgo)) {
            return self::VOTING_MEMBER . ', Stated';
        }

        // Meetings in the past six months
        $meetings = $this->meetingRepository->findPastSixMonths();
        foreach ($meetings as $meeting) {
            // Has Communicated their absence to the meeting
            if ($meeting->getAbsentees()->contains($user)) {
                return self::VOTING_MEMBER . ', Absentee';
            }
            // Attended the meeting
            if ($meeting->getAttendees()->contains($user)) {
                return self::VOTING_MEMBER . ', Attended';
            }
            // Registered a Proxy for the meeting
            foreach ($meeting->getProxies() as $proxy) {
                if ($proxy->getPrincipal() == $user) {
                    return self::VOTING_MEMBER . ', Proxy';
                }
            }
        }

        // Has stated a preference for NONVOTING
        if ($user->getProfile()->getVotingPreference() == VotingPreference::NONVOTING && $user->getProfile()->getVotingPreferenceStatedAt()->isAfter($sixMonthsAgo)) {
            return self::NONVOTING_MEMBER . ', Stated';
        }

        // Has physical accessed the space in the past six months
        foreach ($user->getRfidTags() as $rfidTag) {
            if ($rfidTag->getLastUsed() && $rfidTag->getLastUsed()->isAfter($sixMonthsAgo)) {
                return self::VOTING_MEMBER . ', Physical';
            }
        }

        return self::NONVOTING_MEMBER . ', Automatic';
    }
}
