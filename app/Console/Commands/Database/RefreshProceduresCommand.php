<?php

namespace App\Console\Commands\Database;

use Illuminate\Console\Command;

class RefreshProceduresCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:database:refresh-procedures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop and recreate all database procedures';

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
        //
    }
}
