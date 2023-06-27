<?php

namespace App\Services;

use JsonException;
use App\Models\{Notification, WalletBalance};
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TaskNotificationService extends NotificationService
{
    /**
     * Send notification when admin cancels the task
     *
     * @param $task
     * @return void
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function sendNotificationForCancelledTask($task): void
    {
        switch (true){
            case $task->performer_id :
                // Send notification to selected performer
                UserNotificationService::sendNotificationToPerformer($task);
                break;
            case $task->task_responses()->count() > 0 :
                // Send notification to responses performers
                $responses = $task->task_responses()
                    ->without('user', 'task')
                    ->with('performer:id,name,firebase_token')
                    ->get();

                foreach ($responses as $response) {
                    if ($response->not_free) {
                        WalletBalance::walletBalanceUpdateOrCreate($response->performer_id, setting('admin.pullik_otklik',2000));
                    }
                    $notification = Notification::query()->create([
                        'performer_id' => $response->performer_id,
                        'description' => $task->desciption ?? 'task description',
                        'task_id' => $task->id,
                        "cat_id" => $task->category_id,
                        "name_task" => $task->name,
                        "type" => Notification::CANCELLED_TASK
                    ]);
                    self::pushNotification($response->performer, [
                        'title' => self::titles(Notification::CANCELLED_TASK),
                        'body' => self::descriptions($notification)
                    ], 'notification', (new NotificationService)->notificationResource($notification));
                }
                break;
        }

        // Send notification to task user
        UserNotificationService::sendNotificationToUser($task);
    }
}
