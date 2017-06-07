<?php

namespace App\Console\Commands\Make;

use Illuminate\Console\Command;

class MakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:make
                            {name : Name of the entity to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Entity/Mapping/Interface/Implementation/Factory all in one go.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = trim($this->argument('name'));

        $this->call('hms:make:entity', [
            'name' => "$name",
        ]);

        $this->call('hms:make:mapping', [
            'name' => "$name",
        ]);

        $this->call('hms:make:repository:interface', [
            'name' => "$name",
        ]);

        $this->call('hms:make:repository:implementation', [
            'name' => "$name",
        ]);

        $this->call('hms:make:factory', [
            'name' => "$name",
        ]);
    }
}
