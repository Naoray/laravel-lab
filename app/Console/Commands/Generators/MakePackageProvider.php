<?php

namespace App\Console\Commands\Generators;

use App\Console\Commands\GeneratorCommand;

class MakePackageProvider extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pkgProvider {name} {packagePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Service Provider for a newly created package.';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return resource_path().'/stubs/provider.stub';
    }

    /**
     * @return mixed
     */
    protected function getSourcePath()
    {
        return '/src';
    }
}
