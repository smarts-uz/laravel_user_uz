<?php

namespace App\Services\Task;

use App\Models\Chat\ChMessage;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;

class ReviewService
{
    public static function userReview($task, $request)
    {
        $task->status = $request->status ? Task::STATUS_COMPLETE : Task::STATUS_NOT_COMPLETED;
        $task->save();
        ChMessage::query()->where('from_id', $task->user_id)->where('to_id', $task->performer_id)->delete();
        ChMessage::query()->where('to_id', $task->user_id)->where('from_id', $task->performer_id)->delete();
        $performer = User::query()->find($task->performer_id);
        $performer->increment('reviews');
        Review::query()->create([
            'description' => $request->comment,
            'good_bad' => $request->good,
            'task_id' => $task->id,
            'reviewer_id' => $task->user_id,
            'user_id' => $task->performer_id,
        ]);
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'performer_id' => $task->performer_id,
            'task_id' => $task->id,
            'name_task' => $task->name,
            'description' => 1,
            'type' => Notification::SEND_REVIEW
        ]);
        NotificationService::sendNotificationRequest([$task->performer_id], [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);

        return $notification;
    }


    public static function performerReview($task, $request)
    {
        Review::query()->create([
            'description' => $request->comment,
            'good_bad' => $request->good,
            'task_id' => $task->id,
            'reviewer_id' => $task->performer_id,
            'user_id' => $task->user_id,
            'as_performer' => 1
        ]);
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'performer_id' => $task->performer_id,
            'task_id' => $task->id,
            'name_task' => $task->name,
            'description' => 1,
            'type' => Notification::SEND_REVIEW_PERFORMER
        ]);
        NotificationService::sendNotificationRequest([$task->user_id], [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);
        $task->performer_review = 1;
        $task->save();

        return $notification;
    }
}
