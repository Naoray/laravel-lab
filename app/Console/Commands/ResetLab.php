<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetLab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset {-c|--clearcache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets app to last pushed version.';

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
        exec('git fetch origin && git reset --hard origin/master && git clean -f');
        exec('composer update');

        if ($this->option('clearcache')) {
            exec('composer clearcache');
        }
    }
}
