<?php


namespace App\Services;


use App\Models\Notification;
use App\Models\User;

class TestNotificationService extends NotificationService
{
    public function testPusherNotificationToAll()
    {
        $users = User::query()->where('role_id', User::ROLE_ADMIN)->orWhere('role_id', User::ROLE_MODERATOR)->orWhere('role_id', User::ROLE_PERFORMER)->orWhere('role_id', User::ROLE_USER)->select('id', 'email', 'firebase_token', 'sms_notification', 'email_notification', 'phone_number')->get();
        foreach ($users as $user) {
            $notification = Notification::query()->create([
                'performer_id' => $user->id,
                'description' => 'test notif',
                "type" => 15
            ]);
        }
    }

    public function testPusherNotification(int|string $user_id)
    {

    }
}
