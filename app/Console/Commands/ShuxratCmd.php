<?php

namespace App\Console\Commands;

use App\Mail\MessageEmail;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Services\PerformersService;
use App\Services\Task\TaskService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ShuxratCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shuxrat:run';

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
    //    $a = (new TaskService)->taskIndex('2663');
        $a = (new TaskService)->taskIndex('1058');
        var_dump($a);
    }


    private function performerService() {
        $id = 434;
        $user = User::find($id);
        $authId = $id;
        $service = new PerformersService();
        $item = $service->service($authId, $user);
        dd($item);
    }
    private function reviewobserver() {
        $user = User::find(1);
        $user->review_good = $user->review_good + 1;
        $user->save();
        Review::create([
            'description' => 'description',
            'good_bad' => 1,
            'task_id' => 1,
            'reviewer_id' => 1,
            'user_id' => 1,
        ]);
    }


}
