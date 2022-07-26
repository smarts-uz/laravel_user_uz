<?php

namespace App\Services;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\WalletBalance;

class TaskNotificationService extends NotificationService
{
    /**
     * Send notification when admin cancels the task
     *
     * @param $task
     * @return void
     */
    public static function sendNotificationForCancelledTask($task)
    {
        if ($task->performer_id) { // Send notification to selected performer
            UserNotificationService::sendNotificationToPerformer($task);
        }
        elseif ($task->task_responses()->count() > 0) { // Send notification to responses performers
            $responses = $task->task_responses()
                ->without('user', 'task')
                ->with('performer:id,name,firebase_token')
                ->get();

            foreach ($responses as $response) {
                if ($response->not_free) {
                    WalletBalance::walletBalanceUpdateOrCreate($response->performer_id, setting('admin.pullik_otklik'));
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
                ], 'notification', new NotificationResource($notification));
            }
        }

        // Send notification to task user
        UserNotificationService::sendNotificationToUser($task);
    }
}
