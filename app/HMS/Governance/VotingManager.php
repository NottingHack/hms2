<?php

namespace HMS\Governance;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Governance\MeetingRepository;

class VotingManager
{
    const VOTING_MEMBER = 'Voting Member';
    const NON_VOTING_MEMBER = 'Non-voting Member';
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
     * VotingManager constructor.
     *
     * @param RoleRepository $roleRepository
     * @param MetaRepository $metaRepository
     * @param MeetingRepository $meetingRepository
     */
    public function __construct(
        RoleRepository $roleRepository,
        MetaRepository $metaRepository,
        MeetingRepository $meetingRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->metaRepository = $metaRepository;
        $this->meetingRepository = $meetingRepository;
    }

    /**
     * Check if there is an upcoming Meeting.
     *
     * @return bool
     */
    public static function hasUpcommingMeeting()
    {
        return resolve(MeetingRepository::class)->hasUpcomming();
    }

    /**
     * Count the number of Role::MEMBER_CURRENT Users.
     *
     * @return int
     */
    public function countCurrentMembers()
    {
        return $this->roleRepository
            ->findOneByName(Role::MEMBER_CURRENT)
            ->getUsers()->count();
    }

    /**
     * Count the number of Voting Members.
     *
     * @return int
     */
    public function countVotingMembers()
    {
        return ceil($this->countCurrentMembers() * 0.75); // fake it for now
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
        $currentMembers = $this->countCurrentMembers();

        return ceil($votingMembers * ($quorumPercent / 100));
    }

    /**
     * Get Voting/NonVoting Status For a given User.
     *
     * @param User $user
     * @return string
     */
    public function getVotingStatusForUser(User $user)
    {
        if ($user->cannot('governance.voting.canVote')) {
            return self::CANNOT_VOTE;
        }

        return self::VOTING_MEMBER;
    }
}
