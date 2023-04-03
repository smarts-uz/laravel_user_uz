<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class PusherNotifCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "PusherNotifCommand:run {--user_id : pusher orqali notif jo'natiladigan user id, bunga qiymat kiritilsa role tanlanmasiligi kerak} {--type : pusher orqali notif jo'natiladigan role, agar role tanlansa user id kiritilmaydi} {--title : pusher notif title} {--text : pusher notif text}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'PusherNotifCommand CMD';

    /**
     * Execute the console command.
     *
     * @return int
     */
    /**
     * Execute the console command.
     *
     * @return array
     */
    public function handle()
    {
        $notificationService = new NotificationService();
        $user_id = $this->option("user_id");
        $type = $this->option("type");
        $text = $this->option("text");
        $title = $this->option("title");

        return $notificationService->pusher_notif($type, $title, $text, $user_id);
    }
}
