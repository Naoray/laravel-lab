<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeNewPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:new {name} {--dir=../packages/}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new package and adds it to this lab.';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $packagePath = base_path().'/'.$this->option('dir').$name;

        if ($this->files->isDirectory($packagePath)) {
            return $this->error('The package already exists!');
        }

        $this->files->makeDirectory($packagePath);
        $this->files->put($packagePath.'/composer.json', $this->buildClass($name));

        $this->call('package:add', [
            'name' => $name,
            'path' => $this->option('dir').$name
        ]);
    }

    public function buildClass($name)
    {
        $stub = $this->files->get(resource_path().'/stubs/composer.stub');

        return $this->replaceNamespaces($stub, $name);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespaces(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyPackageName', 'DummyNamespace', 'DummyProviderNamespace'],
            [$this->getPackageName($name), $this->getNamespace($name), $this->getProviderNamespace($name)],
            $stub
        );

        return $stub;
    }

    protected function getPackageName($name)
    {
        return ucfirst(camel_case($name));
    }

    protected function getNamespace($name)
    {
        return $this->getPackageName($name)."\\\\";
    }

    protected function getProviderNamespace($name)
    {
        return $this->getNamespace($name).$this->getPackageName($name).'ServiceProvider';
    }
}
