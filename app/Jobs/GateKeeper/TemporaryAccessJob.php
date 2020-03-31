<?php

namespace App\Jobs\GateKeeper;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\GateKeeper\TemporaryAccessBookingManager;

class TemporaryAccessJob implements ShouldQueue
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
     * @param TemporaryAccessBookingManager $temporaryAccessBookingManager
     *
     * @return void
     */
    public function handle(
        TemporaryAccessBookingManager $temporaryAccessBookingManager
    ) {
        $temporaryAccessBookingManager->updateTemporaryAccessRole();
    }
}
