<?php

namespace App\Console\Commands\Make;

class RepositoryInterfaceMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:make:repository:interface
                            {name : Name of the entity this interface is for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository Interface';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/repository-interface.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        $name = $this->getNamespacedRepositoryInterface($name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }
}
