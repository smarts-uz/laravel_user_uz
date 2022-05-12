<?php

namespace App\Console\Commands;

use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Services\PerformersService;
use App\Services\Task\SearchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class SaidmuhammadCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saidmuhammad:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saidmuhammad CMD';

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
       
        $this-> testTaskSearch();
    }


    private function testTaskSearch() {

        $arr_check = [22,104];

        $item = (new SearchService())->search_new_service($arr_check, '','', null, null, null, null);

        dd($item);
    }
   


}