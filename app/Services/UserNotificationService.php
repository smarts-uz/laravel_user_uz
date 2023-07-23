<?php

namespace App\Services;

use App\Models\Notification;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserNotificationService extends NotificationService
{
    /**
     * admin vazifani bekor qilganda ijrochiga bildisrishnoma yuboradi
     * @param $task
     * @param int $type
     * @return void
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function sendNotificationToPerformer($task, int $type = Notification::ADMIN_CANCEL_TASK): void
    {
        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $task->performer_id,
            'description' => $task->desciption ?? 'task description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => $type
        ]);

        self::sendNotificationRequest($task->performer_id, $notification);
        $locale = (new CustomService)->cacheLang($task->performer_id);
        self::pushNotification($task->performer, [
            'title' => self::titles($type, $locale),
            'body' => self::descriptions($notification, $locale)
        ], 'notification', (new NotificationService)->notificationResource($notification));
    }

    /**
     * admin vazifani bekor qilganda userga bildisrishnoma yuboradi
     * @param $task
     * @param int $type
     * @return void
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function sendNotificationToUser($task, int $type = Notification::ADMIN_CANCEL_TASK): void
    {
        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'description' => $task->desciption ?? 'task description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => $type
        ]);

        self::sendNotificationRequest($task->user_id, $notification);

        $locale = (new CustomService)->cacheLang($task->user_id);
        self::pushNotification($task->user, [
            'title' => self::titles($type, $locale),
            'body' => self::descriptions($notification, $locale)
        ], 'notification',(new NotificationService)->notificationResource($notification));
    }
}
