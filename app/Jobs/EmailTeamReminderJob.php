<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Mail\Teams\TeamReminder;
use HMS\Repositories\RoleRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
     *
     * @return void
     */
    public function handle(RoleRepository $roleRepository)
    {
        // first we need to check which tuesday this is
        $date = Carbon::now();
        if ($date->dayOfWeekIso != 2) {
            // not a Tuesday do nothing
            return;
        }

        if ($date->addDays(8)->day >= 8) {
            // not the Tuesday we want
            return;
        }

        $teams = $roleRepository->findAllTeams();

        foreach ($teams as $team) {
            $to = [['email' => $team->getEmail(), 'name' => $team->getDisplayName()]];

            Mail::to($to)->send(new TeamReminder($team->getDisplayName()));
        }
    }
}
