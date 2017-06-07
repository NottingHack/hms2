<?php

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;

class MappingMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:make:mapping
                            {name : Name of the entity this mapping is for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Mapping';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/mapping.stub';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        parent::fire();
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $namspacedDummmyMapping = str_replace('\\', '.', $name);

        $dummyTable = str_replace('\\', '', Str::snake(Str::plural(str_replace($this->getNamespace($name).'\\', '', $name))));

        $stub = str_replace(
            ['NamspacedDummmyMapping', 'dummyTable'],
            [$namspacedDummmyMapping, $dummyTable],
            $stub
        );

        return $stub;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace('\\', '.', $name);

        return $this->laravel['path'].'/HMS/Mappings/'.str_replace('\\', '/', $name).'.dcm.yml';
    }
}
