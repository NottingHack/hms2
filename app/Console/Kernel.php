<?php

namespace App\Console;

use App\Jobs\EmailTeamReminderJob;
use App\Jobs\Snackspace\LogDebtJob;
use App\Jobs\Banking\MembershipAuditJob;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\Gatekeeper\ZoneOccupantResetJob;
use App\Jobs\Membership\AuditYoungHackersJob;
use App\Jobs\Snackspace\MemberDebtNotificationJob;
use App\Jobs\Governance\RecalculateMeetingQuorumJob;
use App\Jobs\Gatekeeper\UpdateTemporaryAccessRoleJob;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('invites:purge')
                 ->daily();

        $schedule->job(new MembershipAuditJob)
                 ->weekdays()
                 ->dailyAt('23:55');

        $schedule->job(new AuditYoungHackersJob)
                ->dailyAt('06:00');

        $schedule->command('auth:clear-resets')
                ->weekly();

        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->command('passport:purge')->daily();

        $schedule->job(new LogDebtJob)->daily();
        $schedule->job(new ZoneOccupantResetJob)->twiceDaily();
        $schedule->job(new MemberDebtNotificationJob)->monthlyOn(1, '7:00');
        $schedule->job(new EmailTeamReminderJob)->weeklyOn(2, '7:27');
        $schedule->job(new RecalculateMeetingQuorumJob)->everyFiveMinutes()
            ->environments(['local', 'rommie', 'production']);
        $schedule->job(new UpdateTemporaryAccessRoleJob)->everyFiveMinutes()
            ->environments(['local', 'rommie', 'production']);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
