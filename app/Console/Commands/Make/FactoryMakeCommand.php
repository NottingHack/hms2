<?php

namespace App\Console\Commands\Make;

class FactoryMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:make:factory
                            {name : Name of the entity this factory is for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Factory';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/factory.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $name = str_replace($this->getDefaultNamespace($name) . '\\', '', $name);
        $dummyFactoryNamspace = $this->rootNamespace() . '\\Factories\\' . $name;
        $dummyFactoryNamspace = trim(implode('\\', array_slice(explode('\\', $dummyFactoryNamspace), 0, -1)), '\\');

        $stub = str_replace(
            'DummyFactoryNamspace',
            $dummyFactoryNamspace,
            $stub
        );

        return $stub;
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
        $name = str_replace($this->getDefaultNamespace($name) . '\\', '', $name);

        return $this->laravel['path'] . '/HMS/Factories/' . str_replace('\\', '/', $name) . 'Factory.php';
    }
}
