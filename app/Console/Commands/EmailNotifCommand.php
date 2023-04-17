<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class EmailNotifCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "EmailNotifCommand:run {--user_id= : email notif jo'natiladigan user id, bunga qiymat kiritilsa type yozilmasligi kerak} {--type= : email notif jo'natiladigan role, agar role yozilsa user id kiritilmaydi} {--text= : email text}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'EmailNotifCommand CMD';

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

        return $notificationService->email_notif($type, $text, $user_id);
    }
}
