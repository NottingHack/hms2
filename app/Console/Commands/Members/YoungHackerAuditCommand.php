<?php

namespace App\Console\Commands\Members;

use Illuminate\Console\Command;
use App\Events\Membership\YoungHackerAuditRequest;

class YoungHackerAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:members:youngHackerAudit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit young hackers for any that have turned 18';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        event(new YoungHackerAuditRequest());
    }
}
