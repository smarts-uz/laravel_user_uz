<?php

namespace App\Services;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;

class UserNotificationService extends NotificationService
{
    public static function sendNotificationToPerformer($task, $type = Notification::ADMIN_CANCEL_TASK)
    {
        $notification = Notification::query()->create([
            'user_id' => $task->performer_id,
            'description' => $task->desciption ?? 'task description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => $type
        ]);

        self::sendNotificationRequest([$task->performer_id], [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);

        self::pushNotification($task->performer->firebase_token, [
            'title' => self::titles($type),
            'body' => self::descriptions($notification)
        ], 'notification', new NotificationResource($notification));
    }

    public static function sendNotificationToUser($task, $type = Notification::ADMIN_CANCEL_TASK)
    {
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'description' => $task->desciption ?? 'task description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => $type
        ]);

        self::sendNotificationRequest([$task->user_id], [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);

        self::pushNotification($task->user->firebase_token, [
            'title' => self::titles($type),
            'body' => self::descriptions($notification)
        ], 'notification', new NotificationResource($notification));
    }
}
