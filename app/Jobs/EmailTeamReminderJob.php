<?php

namespace App\Jobs;

use App\Mail\Teams\TeamReminder;
use Carbon\Carbon;
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
