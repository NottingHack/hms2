<?php

namespace App\Console;

use Carbon\Carbon;
use HMS\Facades\Meta;
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
        Commands\Invites\PurgeCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // get cutoff date from meta table and pass as argument if set
        $arguments = [];
        try {
            if (Meta::has('purge_cuttoff_interval')) {
                $interval = Meta::get('purge_cuttoff_interval');
                $cutoff_date = Carbon::now()
                    ->sub(new \DateInterval($interval))
                    ->format('Y-m-d');
                $arguments = ['date' => $cutoff_date];
            }
        } catch (\Exception $e) {
            //
        }
        $schedule->command('invites:purge', $arguments)
                 ->daily();
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
