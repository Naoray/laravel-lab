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
    protected $signature = 'pkg:add {name} {path} {--type=path} ';

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
        $type = $this->option('type');

        exec('composer config repositories.'.$name.' '.$type.' '.$path);
        exec('composer require naoray/'.$name);
    }
}
