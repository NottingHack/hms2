<?php

use App\Jobs\PostGitDeployedJob;
use Illuminate\Foundation\Inspiring;
use App\Jobs\GateKeeper\ZoneOccupantResetJob;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('hms:auto-deploy', function () {
    PostGitDeployedJob::dispatchNow();
})->describe('Run the GitDeployedJob');

Artisan::command('hms:reset-zones', function () {
    ZoneOccupantResetJob::dispatchNow();
})->describe('Run the ZoneOccupantResetJob');
