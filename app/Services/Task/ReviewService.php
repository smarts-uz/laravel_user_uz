<?php

namespace App\Services\Task;

use App\Http\Resources\NotificationResource;
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
        if ($request->good == 1) {
            $performer->increment('review_good');
        } else {
            $performer->increment('review_bad');
        }
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
        $task->user->increment('reviews');
        $task->performer_review = 1;
        $task->save();

        return $notification;
    }

    public static function sendReview($task, $request, $status = false)
    {
        if ($task->user_id == auth()->id()) {
            // user review to performer
            $locale = cacheLang($task->user_id);
            if ($status) {
                $request['status'] = 1;
            }
            $notification = ReviewService::userReview($task, $request);
            NotificationService::pushNotification($task->performer, [
                'title' => __('Новый отзыв', [], $locale), 'body' => __('О вас оставлен новый отзыв', [], $locale) . " \"$task->name\" №$task->id"
            ], 'notification', new NotificationResource($notification));

        } elseif ($task->performer_id == auth()->id()) {
            // performer review to user
            $locale = cacheLang($task->performer_id);
            $notification = ReviewService::performerReview($task, $request);
            NotificationService::pushNotification($task->user, [
                'title' => __('Новый отзыв', [], $locale), 'body' => __('О вас оставлен новый отзыв', [], $locale) . " \"$task->name\" №$task->id"
            ], 'notification', new NotificationResource($notification));
        }
    }
}
