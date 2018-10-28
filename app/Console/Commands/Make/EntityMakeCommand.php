<?php

namespace App\Console\Commands\Make;

class EntityMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:make:entity
                            {name : Name of the entity to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Entity';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/entity.stub';
    }
}
