<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Permissions\ListCommand::class,
        Commands\Permissions\AddCommand::class,
        Commands\Permissions\RemoveCommand::class,
        Commands\Permissions\StripCommand::class,
        Commands\Permissions\SyncCommand::class,
        Commands\Invites\PurgeCommand::class,
        Commands\Permissions\DefaultsCommand::class,
        Commands\Make\MakeCommand::class,
        Commands\Make\EntityMakeCommand::class,
        Commands\Make\MappingMakeCommand::class,
        Commands\Make\RepositoryInterfaceMakeCommand::class,
        Commands\Make\RepositoryImplementationMakeCommand::class,
        Commands\Make\FactoryMakeCommand::class,
        Commands\Banking\AuditCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('invites:purge')
                 ->daily();

        $schedule->command('hms:members:audit')
                 ->weekdays()
                 ->dailyAt('23:55');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
