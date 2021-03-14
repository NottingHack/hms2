<?php

namespace App\Jobs\Governance;

use HMS\Governance\VotingManager;
use HMS\Repositories\Governance\MeetingRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateMeetingQuorumJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * Updates the counts on the next Meeting.
     *
     * @return void
     */
    public function handle(
        MeetingRepository $meetingRepository,
        VotingManager $votingManager
    ) {
        $meetings = $meetingRepository->findFuture();

        if (empty($meetings)) {
            return;
        }

        $currentMembers = $votingManager->countCurrentMembers();
        $votingMembers = $votingManager->countVotingMembers();
        $currentQuorumRequirement = $votingManager->currentQuorumRequirement();

        foreach ($meetings as $meeting) {
            $meeting->setCurrentMembers($currentMembers);
            $meeting->setVotingMembers($votingMembers);
            $meeting->setQuorum($currentQuorumRequirement);
            $meetingRepository->save($meeting);
        }
    }
}
