<?php

namespace App\Console\Commands;

use App\Services\TestNotificationService;
use Illuminate\Console\Command;

class TestPusherNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pusher {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pusher notification command for testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notification = new TestNotificationService();

        $user_id = $this->argument('user_id');

        if ($user_id === null) {
            $notification->testPusherNotificationToAll();
        }else {
            $notification->testPusherNotification($user_id);
        }

    }
}
