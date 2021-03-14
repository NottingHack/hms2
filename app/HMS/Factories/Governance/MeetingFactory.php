<?php

namespace HMS\Factories\Governance;

use Carbon\Carbon;
use HMS\Entities\Governance\Meeting;
use HMS\Governance\VotingManager;
use HMS\Repositories\Governance\MeetingRepository;

class MeetingFactory
{
    /**
     * @var MeetingRepository
     */
    protected $meetingRepository;

    /**
     * @var VotingManager
     */
    protected $votingManager;

    /**
     * @param MeetingRepository $meetingRepository
     * @param VotingManager $votingManager
     */
    public function __construct(
        MeetingRepository $meetingRepository,
        VotingManager $votingManager
    ) {
        $this->meetingRepository = $meetingRepository;
        $this->votingManager = $votingManager;
    }

    /**
     * Function to instantiate a new Meeting from given params.
     *
     * @param string $title
     * @param Carbon $startTime
     * @param bool $extrordinary
     */
    public function create(
        string $title,
        Carbon $startTime,
        bool $extraordinary
    ) {
        $_meeting = new Meeting();

        $_meeting->setTitle($title);
        $_meeting->setStartTime($startTime);
        $_meeting->setExtraordinary($extraordinary);
        $_meeting->setCurrentMembers($this->votingManager->countCurrentMembers());
        $_meeting->setVotingMembers($this->votingManager->countVotingMembers());
        $_meeting->setQuorum($this->votingManager->currentQuorumRequirement());

        return $_meeting;
    }
}
