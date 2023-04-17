<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SMSNotifCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "SMSNotifCommand:run {--user_id= : sms notif jo'natiladigan user id, bunga qiymat kiritilsa type yozilmasligi kerak} {--type= : sms notif jo'natiladigan role, agar role yozilsa user id kiritilmaydi} {--text= : sms text}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SMSNotifCommand CMD';

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

        return $notificationService->sms_notif($type, $text, $user_id);
    }
}
