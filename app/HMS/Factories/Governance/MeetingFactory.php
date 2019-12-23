<?php

namespace HMS\Factories\Governance;

use Carbon\Carbon;
use HMS\Governance\VotingManager;
use HMS\Entities\Governance\Meeting;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\Governance\MeetingRepository;

class MeetingFactory
{
    /**
     * @var MeetingRepository
     */
    protected $meetingRepository;
    protected $metaRepository;
    protected $votingManager;

    /**
     * @param MeetingRepository $meetingRepository
     */
    public function __construct(
        MeetingRepository $meetingRepository,
        MetaRepository $metaRepository,
        VotingManager $votingManager
    ) {
        $this->meetingRepository = $meetingRepository;
        $this->metaRepository = $metaRepository;
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
