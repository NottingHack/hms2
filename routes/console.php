<?php

use App\Jobs\PostGitDeployedJob;
use Illuminate\Foundation\Inspiring;
use App\Jobs\Banking\MembershipAuditJob;
use App\Jobs\GateKeeper\ZoneOccupantResetJob;
use App\Jobs\Membership\AuditYoungHackersJob;

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
})->describe('Audit Zone Occupant and reset people back to Off-site');

Artisan::command('hms:members:audit', function () {
    MembershipAuditJob::dispatch();
})->describe('Audit the members against current banking records');

Artisan::command('hms:members:youngHackerAudit', function () {
    AuditYoungHackersJob::dispatchNow();
})->describe('Audit young hackers for any that have turned 18');

Artisan::command('hms:reseed {--force}', function ($force) {
    if (! App::environment('local') && ! $force) {
        // The environment is not local
        $this->error('Abort, not local environment (use --force to override)');

        return;
    }

    exec('mysql -uroot -proot mailserver -e "DELETE FROM alias"');
    exec('mysql -uroot -proot mailserver -e "DELETE FROM mailbox"');

    $this->call('migrate:reset');
    $this->call('doctrine:migrations:refresh');
    $this->call('migrate');
    $this->call('hms:database:refresh-views');
    $this->call('hms:database:refresh-procedures');
    $this->call('permissions:defaults');
    $this->call('db:seed');
    $this->call('passport:install');
})->describe('Reseed the database');
