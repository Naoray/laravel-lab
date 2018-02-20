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
                             {name? : The name of the package}
                             {vendor? : Vendor name of the package}
                             {dir? : Directory where the package will be stored}
                             {author? : Author of the package}
                             {mail? : Author\'s email address}
                             {copyright? : Copyright will be placed in the LICENSE file}';

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
     * Name of the package.
     * @var String
     */
    protected $packageName;

    /**
     * Directory where the package will be stored.
     * @var String
     */
    protected $dir;

    /**
     * Copyright fields which will be placed inside the License.md file.
     * @var String
     */
    protected $copyright;

    /**
     * The packages vendor name.
     * @var String
     */
    protected $vendor;

    /**
     * The packages author.
     * @var String
     */
    protected $author;

    /**
     * The mantainer's email address.
     * @var String
     */
    protected $mail;

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
        $this->checkForInputs();

        if (! $this->confirm('Do you wish to continue?')) {
            return $this->error('Canceled command!');
        }

        $packagePath = $this->getPackagePath();

        if ($this->alreadyExists($packagePath)) {
            return $this->error('The package already exists!');
        }

        $this->createDirectories($packagePath);

        $this->createCommonFiles($packagePath);

        $this->createComposer($packagePath);
        $this->createServiceProvider($packagePath);
        $this->createTestCase($packagePath);

        $this->callSilent('package:add', [
            'name' => $this->packageName,
            'path' => $this->dir.$this->packageName,
            'vendor' => $this->vendor,
            '--without-interaction' => true,
        ]);
    }

    /**
     * Checks for needed input and prints it out.
     */
    public function checkForInputs()
    {
        $name = $this->getNameInput();
        $vendor = $this->getVendorInput();
        $dir = $this->getDirectoryInput();
        $author = $this->getAuthorInput();
        $mail = $this->getMailInput();
        $copyright = $this->getCopyrightInput();

        $this->table(
            ['Name', 'Vendor', 'Directory', 'Author', 'E-mail', 'Copyright'],
            [
                [$name, $vendor, $dir, $author, $mail, $copyright]
            ]
        );
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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createCommonFiles($path)
    {
        $this->files->put($path.'/readme.md', $this->buildFile('readme'));
        $this->files->put($path.'/LICENSE.md', $this->buildFile('LICENSE'));
        $this->files->put($path.'/CONTRIBUTING.md', $this->buildFile('CONTRIBUTING'));
        $this->files->put($path.'/.travis.yml', $this->buildFile('.travis'));
        $this->files->put($path.'/phpunit.xml', $this->buildFile('phpunit'));
        $this->files->put($path.'/phpunit.xml', $this->buildFile('.gitignore'));
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
            $path.'/src/'.$this->getPackageName().'ServiceProvider.php',
            $this->buildFile('src/provider')
        );

        $this->info('Service Provider created successfully!');
    }

    protected function createTestCase($path)
    {
        $this->files->put(
            $path.'/tests/TestCase.php',
            $this->buildFile('tests/TestCase')
        );

        $this->info('Test Case created sucessfully!');
    }

    /**
     * @param $name
     * @return MakePackage
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildFile($name)
    {
        $stub = $this->files->get(resource_path().'/stubs/'.$name.'.stub');

        return $this->replaceNamespaces($stub)
            ->replaceNames($stub)
            ->replaceCredentials($stub);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceNamespaces(&$stub)
    {
        $stub = str_replace(
            ['DummyComposerNamespace', 'DummyComposerProviderNamespace', 'DummyRootNamespace'],
            [$this->getComposerNamespace(), $this->getComposerProviderNamespace(), $this->getRootNamespace()],
            $stub
        );

        return $this;
    }

    /**
     * Replace the names for the given stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceNames(&$stub)
    {
        $stub = str_replace(
            ['DummyVendorName', 'DummyPackageName', 'DummyClass', 'CompanyOrVendorName'],
            [$this->vendor, $this->packageName, $this->getPackageName().'ServiceProvider', $this->copyright],
            $stub
        );

        return $this;
    }

    /**
     * Replace Author credentials.
     *
     * @param $stub
     * @return mixed
     */
    protected function replaceCredentials(&$stub)
    {
        $stub = str_replace(
            ['DummyAuthorName', 'DummyAuthorEmail'],
            [$this->author, $this->mail],
            $stub
        );

        return $stub;
    }

    /**
     * @return string
     */
    protected function getRootNamespace()
    {
        return ucfirst($this->vendor).'\\'.$this->getPackageName();
    }

    /**
     * @return string
     */
    protected function getPackageName()
    {
        return ucfirst(camel_case($this->packageName));
    }

    /**
     * @return string
     */
    protected function getComposerNamespace()
    {
        return ucfirst($this->vendor)."\\\\".$this->getNamespace();
    }

    /**
     * @return string
     */
    protected function getNamespace()
    {
        return ucfirst(camel_case($this->packageName))."\\\\";
    }

    /**
     * @return string
     */
    protected function getComposerProviderNamespace()
    {
        return $this->getComposerNamespace().$this->getPackageName().'ServiceProvider';
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
        return base_path().'/'.$this->dir.$this->packageName;
    }

    /**
     * Get the desired directory path from the input.
     *
     * @return string
     */
    protected function getDirectoryInput()
    {
        if ($this->dir) return $this->dir;

        if (! $this->dir = $this->argument('dir')) {
            $this->dir = $this->anticipate('Where should the package be installed?', ['../packages/']);
        }

        return $this->dir;;
    }

    /**
     * Get copyright input.
     * @return String
     */
    public function getCopyrightInput()
    {
        if ($this->copyright) return $this->copyright;

        if (! $this->copyright = $this->argument('copyright')) {
            $this->copyright = $this->ask('Who will hold the copyrights?');
        }

        return $this->copyright;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        if ($this->packageName) return $this->packageName;

        if (! $this->packageName = trim($this->argument('name'))) {
            $this->packageName = $this->ask('What\'s your packages name?');
        }

        return $this->packageName;
    }

    /**
     * Get the desired vendor name from the input.
     *
     * @return string
     */
    protected function getVendorInput()
    {
        if ($this->vendor) return $this->vendor;

        if (! $this->vendor = trim($this->argument('vendor'))) {
            $this->vendor = $this->ask('What\'s the packages github name (vendor name of the package)?');
        }

        return $this->vendor;
    }

    /**
     * Get the author name input.
     * @return String
     */
    public function getAuthorInput()
    {
        if ($this->author) return $this->author;

        if (! $this->author = $this->argument('author')) {
            $this->author = $this->ask('Who is the author of the package?');
        }

        return $this->author;
    }

    /**
     * Get mail input.
     * @return String
     */
    public function getMailInput()
    {
        if ($this->mail) return $this->mail;

        if (! $this->mail = $this->argument('mail')) {
            $this->mail = $this->ask('What\'s the mantainer\'s e-mail?');
        }

        return $this->mail;
    }
}
