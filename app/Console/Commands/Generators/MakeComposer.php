<?php

namespace App\Console\Commands\Generators;

use App\Console\Commands\GeneratorCommand;

class MakeComposer extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:composer {name} {packagePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new composer file for a newly created package.';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return resource_path().'/stubs/composer.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return '';
    }

    /**
     * @return mixed
     */
    protected function getSourcePath()
    {
        return '';
    }
}
