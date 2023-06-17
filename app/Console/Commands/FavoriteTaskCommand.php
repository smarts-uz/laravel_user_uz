<?php

namespace App\Console\Commands;

use App\Services\Task\SearchService;
use Illuminate\Console\Command;

class FavoriteTaskCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TaskFavorite:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $searchService = new SearchService();
        $userId = 1;
        $data = $searchService->favorite_task_all($userId);
        return dd($data);
    }
}
