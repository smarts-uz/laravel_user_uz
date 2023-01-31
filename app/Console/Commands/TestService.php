<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
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
        $type = '';
        $title = 'test title';
        $text = 'test text';
        $user_id = 655;
        $notificationService = new NotificationService();
        $data = $notificationService->firebase_notif($type,$title,$text,$user_id);
        dd($data);
    }
}
