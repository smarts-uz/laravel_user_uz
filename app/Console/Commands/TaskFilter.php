<?php

namespace App\Console\Commands;

use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Services\PerformersService;
use App\Services\Task\FilterTaskService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TaskFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sardor CMD';

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
     * @return int
     */
    public function handle()
    {
        $filterTask = new FilterTaskService();
        $data = $filterTask->filter([]);
        dd($data);
    }


}
