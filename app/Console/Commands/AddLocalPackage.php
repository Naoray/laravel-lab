<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AddLocalPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:add {name?} {path?} {vendor?} {--type=path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds local package with repository option.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = $this->argument('path');
        $vendor = $this->argument('vendor');
        $type = $this->option('type');

        if (! $vendor) {
            $vendor = $this->ask('What is your package\'s vendor name?');
        }

        if (! $name) {
            $name = $this->ask('What is your package\'s name?');
        }

        if (! $path) {
            $path = $this->ask('What is your package\'s path?');
        }

        $this->table(['vendor', 'name', 'path', 'type'], [[$vendor, $name, $path, $type]]);
        if (! $this->confirm('Do you wish to continue?')) {
            return;
        }

        exec('composer config repositories.'.$name.' '.$type.' '.$path);
        exec('composer require "'.$vendor.'/'.$name.':dev-master"');
    }
}
