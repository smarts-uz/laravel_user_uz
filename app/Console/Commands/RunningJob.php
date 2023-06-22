<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunningJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RunningJob:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RunningJobCommand CMD';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('queue:listen');
    }
}
