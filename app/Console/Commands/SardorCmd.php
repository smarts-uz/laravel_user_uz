<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportController;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Services\PerformersService;
use App\Services\Task\CategoriesAPIService;
use App\Services\Task\SearchService;
use App\Services\Task\TaskService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class SardorCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sardor:run';

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
        (new TaskService)->taskIndex(2585);
    }

    private function testTaskSearch() {

        $arr_check = [22,104];

        $item = (new SearchService())->search_new_service($arr_check, '','', null, null, null, null);

        dd($item);
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
