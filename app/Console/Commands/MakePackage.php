<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakePackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:package 
                             {name : The name of the package}
                             {--dir=../packages/ : Directory where the package will be stored}
                             {--C|copyright=byte5 digital media GmbH : Copyright will be placed in the LICENSE file}
                             {vendor=byte5digital : Vendor name of the package}';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new package and adds it to this lab.';

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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $packagePath = $this->getPackagePath();

        if ($this->alreadyExists($packagePath)) {
            return $this->error('The package already exists!');
        }

        $this->createDirectories($packagePath);

        $this->createCommonFiles($packagePath);

        $this->createComposer($packagePath);
        $this->createServiceProvider($packagePath);

        $this->call('package:add', [
            'name' => $this->getNameInput(),
            'path' => $this->getDirectoryInput().$this->getNameInput(),
            '--vendor' => $this->getVendorInput(),
        ]);
    }

    /**
     * @param $path
     */
    protected function createDirectories($path)
    {
        $this->files->makeDirectory($path);
        $this->info('Package directory created successfully!');
        $this->files->makeDirectory($path.'/src');
        $this->info('Source directory created successfully!');
        $this->files->makeDirectory($path.'/tests');
        $this->info('Tests directory created successfully!');
    }

    /**
     * Create common files.
     *
     * @param $path
     */
    protected function createCommonFiles($path)
    {
        $this->files->put($path.'/readme.md', $this->buildFile('readme'));
        $this->files->put($path.'/LICENSE.md', $this->buildFile('LICENSE'));
        $this->files->put($path.'/CONTRIBUTION.md', $this->buildFile('CONTRIBUTION'));
        $this->files->put($path.'/.travis.yml', $this->buildFile('.travis'));
        $this->files->put($path.'/phpunit.xml', $this->buildFile('phpunit'));
        $this->info('Common files created successfully!');
    }

    /**
     * @param $path
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createComposer($path)
    {
        $this->files->put($path.'/composer.json', $this->buildFile('composer'));
        $this->info('Composer created successfully!');
    }

    /**
     * @param $path
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createServiceProvider($path)
    {
        $this->files->put(
            $path.'/src/'.$this->getPackageName($this->getNameInput()).'ServiceProvider.php',
            $this->buildFile('provider')
        );
        $this->info('Service Provider created successfully!');
    }

    /**
     * @param $name
     * @return MakePackage
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildFile($name)
    {
        $packageName = $this->getNameInput();
        $stub = $this->files->get(resource_path().'/stubs/'.$name.'.stub');

        return $this->replaceNamespaces($stub, $packageName)->replaceNames($stub, $packageName);
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
            ['DummyComposerNamespace', 'DummyComposerProviderNamespace', 'DummyRootNamespace'],
            [$this->getComposerNamespace($name), $this->getComposerProviderNamespace($name), $this->getRootNamespace($name)],
            $stub
        );

        return $this;
    }

    /**
     * Replace the names for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNames(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyVendorName', 'DummyPackageName', 'DummyClass', 'CompanyOrVendorName'],
            [$this->getVendorInput(), $name, $this->getPackageName($name).'ServiceProvider', $this->option('copyright')],
            $stub
        );

        return $stub;
    }

    /**
     * @param $name
     * @return string
     */
    protected function getRootNamespace($name)
    {
        return ucfirst($this->getVendorInput()).'\\'.$this->getPackageName($name);
    }

    /**
     * @param $name
     * @return string
     */
    protected function getPackageName($name)
    {
        return ucfirst(camel_case($name));
    }

    /**
     * @param $name
     * @return string
     */
    protected function getComposerNamespace($name)
    {
        return ucfirst($this->getVendorInput())."\\\\".$this->getNamespace($name);
    }

    /**
     * @param $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return $this->getPackageName($name)."\\\\";
    }

    /**
     * @param $name
     * @return string
     */
    protected function getComposerProviderNamespace($name)
    {
        return $this->getComposerNamespace($name).$this->getPackageName($name).'ServiceProvider';
    }

    /**
     * Determine if the class already exists.
     *
     * @param $path
     * @return bool
     */
    protected function alreadyExists($path)
    {
        return $this->files->isDirectory($path);
    }

    /**
     * @return string
     */
    protected function getPackagePath()
    {
        return base_path().'/'.$this->getDirectoryInput().$this->getNameInput();
    }

    /**
     * Get the desired directory path from the input.
     *
     * @return string
     */
    protected function getDirectoryInput()
    {
        return trim($this->option('dir'));
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }

    /**
     * Get the desired vendor name from the input.
     *
     * @return string
     */
    protected function getVendorInput()
    {
        return trim($this->argument('vendor'));
    }
}
