<?php

namespace App\Console\Commands\Banking;

use Illuminate\Console\Command;
use App\Events\Banking\AuditRequest;

class AuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:members:audit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit the members aginst current banking records';

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
        event(new AuditRequest());
    }
}
