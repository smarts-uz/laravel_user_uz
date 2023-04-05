<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class FirebaseNotifCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "FirebaseNotifCommand:run {--user_id= : push notif  jo'natiladigan user id, bunga qiymat kiritilsa role tanlanmasiligi kerak} {--type= : push notif jo'natiladigan role, agar role tanlansa user id kiritilmaydi} {--title= : push notif title} {--text= : push notif text}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'FirebaseNotifCommand CMD';

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

        return $notificationService->firebase_notif($type, $title, $text, $user_id);
    }
}
