<?php

namespace App\Console\Commands;

use App\Models\BlogNew;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class BlogNewNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BlogNewNotify:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * test firebase notification
     * Execute the console command.
     *
     * @return int
     * @throws \JsonException
     */
    public function handle()
    {
     /*   $type = 'default';
        $title = 'test title';
        $text = 'test text';
        $user_id = 655;
        $notificationService = new NotificationService();
        $data = $notificationService->firebase_notif($type,$title,$text,$user_id);
        dd($data);*/

        $news = new BlogNew();
        $news->title = 'title';
        $news->desc = 'desc';

        NotificationService::sendNotification($news, true);
    }

//    /**
//     * test pusher notification
//     * Execute the console command.
//     *
//     * @return int
//     */
//    public function handle()
//    {
//        $type = 'default';
//        $title = 'test title';
//        $text = 'test text';
//        $user_id = 655;
//        $notificationService = new NotificationService();
//        $data = $notificationService->pusher_notif($type,$title,$text,$user_id);
//        dd($data);
//    }

//    /**
//     * test sms notification
//     * Execute the console command.
//     *
//     * @return int
//     */
//    public function handle()
//    {
//        $type = 'default';
//        $text = 'test text';
//        $user_id = 655;
//        $notificationService = new NotificationService();
//        $data = $notificationService->sms_notif($type,$text,$user_id);
//        dd($data);
//    }

//    /**
//     * test email notification
//     * Execute the console command.
//     *
//     * @return int
//     */
//    public function handle()
//    {
//        $type = 'default';
//        $text = 'test text';
//        $user_id = 655;
//        $notificationService = new NotificationService();
//        $data = $notificationService->email_notif($type,$text,$user_id);
//        dd($data);
//    }

}
