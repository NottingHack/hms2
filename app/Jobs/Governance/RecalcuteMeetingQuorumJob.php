<?php

namespace App\Jobs\Governance;

use Illuminate\Bus\Queueable;
use HMS\Governance\VotingManager;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Repositories\Governance\MeetingRepository;

class RecalcuteMeetingQuorumJob implements ShouldQueue
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
        $currentMembers = $votingManager->countCurrentMembers();
        $votingMembers = $votingManager->countVotingMembers();
        $currentQuorumRequirement = $votingManager->currentQuorumRequirement();

        $meetings = $meetingRepository->findFuture();

        foreach ($meetings as $meeting) {
            $meeting->setCurrentMembers($currentMembers);
            $meeting->setVotingMembers($votingMembers);
            $meeting->setQuorum($currentQuorumRequirement);
            $meetingRepository->save($meeting);
        }
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
}
