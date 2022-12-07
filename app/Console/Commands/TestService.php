<?php

namespace App\Console\Commands;

use App\Models\User;
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
        $categories = [22,23,24,25,26,27,28,29];
        /** @var User $user */
        $user = 655;
        $sms_notification = 1;
        $email_notification = 0;
        $subscribeToCategory = new ProfileService();
        $data = $subscribeToCategory->subscribeToCategory($categories, $user, $sms_notification, $email_notification);
        dd($data);
    }
}
