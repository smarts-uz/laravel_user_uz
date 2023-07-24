<?php

namespace Tests\Unit;

use App\Models\BlogNew;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    public function test_getNotifications()
    {
        $user = User::find(1);
        NotificationService::getNotifications($user);
        $this->assertTrue(true);
    }

    public function test_getNotifService()
    {
        $user = User::find(1);
        (new NotificationService)->getNotifService($user);
        $this->assertTrue(true);
    }

//    /**
//     * @throws ContainerExceptionInterface
//     * @throws NotFoundExceptionInterface
//     * @throws JsonException
//     */
//    public function test_sendTaskNotification()
//    {
//        $task = Task::find(3033);
//        $user_id = 1;
//        NotificationService::sendTaskNotification($task, $user_id);
//        $this->assertTrue(true);
//    }

//    /**
//     * @throws JsonException
//     */
//    public function test_sendNotification()
//    {
//        $data = BlogNew::find(34);
//        NotificationService::sendNotification($data);
//        $this->assertTrue(true);
//    }

    /**
     * @throws JsonException
     */
    public function test_sendNotificationRequest()
    {
        $user_id = 1;
        $notification = Notification::find(51376);
        NotificationService::sendNotificationRequest($user_id, $notification);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function test_sendResponseToTaskNotification()
    {
        $task = Task::find(3033);
        $user = User::find(1);
        NotificationService::sendResponseToTaskNotification($task, $user);
        $this->assertTrue(true);
    }

    public function test_sendBalanceReplenished()
    {
        $user_id = 1;
        $amount = 12000;
        $payment_system = 'payme';
        $transaction_id = 67;
        NotificationService::sendBalanceReplenished($user_id, $amount, $payment_system, $transaction_id);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_pushNoti()
    {
        $user = User::find(1);
        $notification = Notification::find(51376);
        NotificationService::pushNoti($user, $notification);
        $this->assertTrue(true);
    }

    public function test_firebase_notif()
    {
        $type = 'role_admin';
        $title = 'title';
        $text = 'text';
        $user_id = 1;
        (new NotificationService)->firebase_notif($type, $title, $text, $user_id);
        $this->assertTrue(true);
    }

    /**
     * @throws JsonException
     */
    public function test_pusher_notif()
    {
        $type = 'role_admin';
        $title = 'title';
        $text = 'text';
        $user_id = 1;
        (new NotificationService)->pusher_notif($type, $title, $text, $user_id);
        $this->assertTrue(true);
    }

    public function test_sms_notif()
    {
        $type = 'role_admin';
        $text = 'text';
        $user_id = 1;
        (new NotificationService)->sms_notif($type, $text, $user_id);
        $this->assertTrue(true);
    }

    public function test_email_notif()
    {
        $type = 'role_admin';
        $text = 'text';
        $user_id = 1;
        (new NotificationService)->email_notif($type, $text, $user_id);
        $this->assertTrue(true);
    }

    public function test_task_create_notification()
    {
        $user_id = 1;
        $task_id = 1000;
        $task_name = 'task name';
        $task_category_id = 31;
        $title = 'title';
        $body = 'body';
        (new NotificationService)->task_create_notification($user_id, $task_id, $task_name, $task_category_id, $title, $body);
        $this->assertTrue(true);
    }

    public function test_pushNotification()
    {
        $user = User::find(1);
        $locale = 'uz';
        $notification = Notification::find(51376);
        NotificationService::pushNotification($user, [
            'title' => NotificationService::titles($notification->type, $locale),
            'body' => NotificationService::descriptions($notification, $locale)
        ], 'notification', (new NotificationService)->notificationResource($notification));
        $this->assertTrue(true);
    }

    public function test_titles()
    {
        $type = Notification::TASK_CREATED;
        $locale = 'uz';
        NotificationService::titles($type, $locale);
        $this->assertTrue(true);
    }

    public function test_descriptions()
    {
        $notification = Notification::find(51376);
        $locale = 'uz';
        NotificationService::descriptions($notification, $locale);
        $this->assertTrue(true);
    }

    public function test_readAllNotifications()
    {
        $user_id = 1;
        NotificationService::readAllNotifications($user_id);
        $this->assertTrue(true);
    }

}
