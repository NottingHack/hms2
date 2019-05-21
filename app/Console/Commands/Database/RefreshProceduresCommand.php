<?php

namespace App\Console\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $databaseName = DB::getDatabaseName();
        $databaseUsername = DB::getConfig('username');
        $hostname = gethostname();
        if ($hostname == 'hmsdev') {
            $hostname = '%';
        } else {
            $hostname .= '.lspace';
        }

        $proceduresDirectory = config('hms.procedures_directory');
        $this->info("Creating procedures form {$proceduresDirectory}");

        $procedureSqlFiles = preg_grep('~\.sql$~', scandir($proceduresDirectory));

        foreach ($procedureSqlFiles as $procedureFile) {
            $this->info('Creating procedures: ' . $procedureFile);

            $spname = basename($procedureFile, '.sql');
            if (substr($spname, 0, 3) == 'fn_') {
                $this->info('Skipping FUNCTION');
                continue; // Bail on functions as they need SUPER
                $dropQuery = "DROP FUNCTION IF EXISTS $spname";
                $grantQuery = "GRANT EXECUTE ON FUNCTION $spname TO '$databaseUsername'@'$hostname'";
            } else {
                $dropQuery = "DROP PROCEDURE IF EXISTS $spname";
                $grantQuery = "GRANT EXECUTE ON PROCEDURE $spname TO '$databaseUsername'@'$hostname'";
            }

            $sql = file_get_contents($proceduresDirectory . DIRECTORY_SEPARATOR . $procedureFile);
            preg_match_all('/CREATE.*END/ms', $sql, $createArray);
            $createQuery = $createArray[0][0];

            DB::unprepared($dropQuery);
            DB::unprepared($createQuery);
            DB::unprepared($grantQuery);
        }
    }
}
