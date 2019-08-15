<?php

namespace App\Console\Commands\Make;

class RepositoryImplementationMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:make:repository:implementation
                            {name : Name of the entity this implementation if for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository Implementation';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository Implementation';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/doctrine-repository.stub';
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
        $name = $this->getNamespacedRepositoryImplementation($name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }
}
