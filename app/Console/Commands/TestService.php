<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Chat\ContactService;
use App\Services\PerformersService;
use App\Services\Profile\ProfileService;
use Illuminate\Console\Command;

class TestService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test:service';

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

        $performer_filter = new PerformersService();
        $data = $performer_filter->performer_filter([]);
        dd($data);
    }
}
