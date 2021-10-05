<?php

namespace App\Jobs;

use App\Mail\Teams\TeamReminder;
use Carbon\Carbon;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailTeamReminderJob implements ShouldQueue
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
     *
     * @param RoleRepository $roleRepository
     * @param MetaRepository $metaRepository
     *
     * @return void
     */
    public function handle(
        RoleRepository $roleRepository,
        MetaRepository $metaRepository
    ) {
        $membersMeetingSchedule = $metaRepository->get('members_meeting_schedule', 'first wednesday');
        $membersMeetingReminderDaysBefore = $metaRepository->get('members_meeting_reminder_days_before', 8);

        $date = Carbon::today();
        $thisMonthsMeetingDate = new Carbon($membersMeetingSchedule . ' of this month');
        $nextMonthsMeetingDate = new Carbon($membersMeetingSchedule . ' of next month');

        if ($thisMonthsMeetingDate->copy()->subDays($membersMeetingReminderDaysBefore) != $date
            && $nextMonthsMeetingDate->copy()->subDays($membersMeetingReminderDaysBefore) != $date) {
            return;
        }

        $teams = $roleRepository->findAllTeams();

        foreach ($teams as $team) {
            if (is_null($team->getEmail())) {
                continue;
            }

            $to = [['email' => $team->getEmail(), 'name' => $team->getDisplayName()]];

            Mail::to($to)->send(new TeamReminder($team->getDisplayName()));
        }
    }
}
