<?php

namespace App\Console\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:database:refresh-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop and recreate all database views';

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
        $dropQuery =
            'SET @views = NULL;' .
            'SELECT GROUP_CONCAT(table_schema, \'.\', table_name) INTO @views' .
            ' FROM information_schema.views' .
            ' WHERE table_schema = \''.$databaseName.'\';' .
            'SET @views = IFNULL(CONCAT(\'DROP VIEW \', @views), \'SELECT \"No Views\"\');' .
            'PREPARE stmt FROM @views;' .
            'EXECUTE stmt;' .
            'DEALLOCATE PREPARE stmt;';

        $this->info('Dropping all current views');
        DB::unprepared($dropQuery);

        $viewsDirectory = config('hms.views_directory');
        $this->info("Creating views form {$viewsDirectory}");

        $viewSqlFiles = preg_grep('~\.sql$~', scandir($viewsDirectory));

        foreach ($viewSqlFiles as $viewFile) {
            $this->info('Creating view: ' . $viewFile);
            $sql = file_get_contents($viewsDirectory.DIRECTORY_SEPARATOR.$viewFile);
            // dump($sql);
            DB::unprepared($sql);
        }
    }
}
