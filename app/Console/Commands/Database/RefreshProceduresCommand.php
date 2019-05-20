<?php

namespace App\Console\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

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
        $pdo = DB::getPdo();
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
            $sql = file_get_contents($proceduresDirectory . DIRECTORY_SEPARATOR . $procedureFile);

            $sql = str_replace('DELIMITER //', '', $sql);
            $sql = str_replace(" //\nDELIMITER ;", '', $sql);

            $pdo->exec($sql);

            $spname = basename($procedureFile, '.sql');
            if (substr($spname, 0, 3) == 'fn_') {
                $query = "GRANT EXECUTE ON FUNCTION $spname TO '$databaseUsername'@'$hostname'";
            } else {
                $query = "GRANT EXECUTE ON PROCEDURE $spname TO '$databaseUsername'@'$hostname'";
            }
            try {
                DB::unprepared($query);
            } catch (QueryException  $e) {
                Log::warning($e->getMessage());
            }
        }
    }
}
