<?php

namespace HMS\Governance;

use HMS\Entities\Role;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;

class VotingManager
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * VotingManager constructor.
     *
     * @param RoleRepository $roleRepository
     * @param MetaRepository $metaRepository
     */
    public function __construct(RoleRepository $roleRepository, MetaRepository $metaRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->metaRepository = $metaRepository;
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
}
