<?php

namespace App\Services\Task;

use App\Models\{ChMessage, Notification, Review, Task, User};
use App\Services\{CustomService, NotificationService};
use JsonException;
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};

class ReviewService
{

    /**
     * task user review
     * @param $task
     * @param $request
     * @return Notification
     * @throws JsonException
     */
    public static function userReview($task, $request): Notification
    {
        $task->status = $request->status ? Task::STATUS_COMPLETE : Task::STATUS_NOT_COMPLETED;
        $task->save();
        ChMessage::query()->where('from_id', $task->user_id)->where('to_id', $task->performer_id)->delete();
        ChMessage::query()->where('to_id', $task->user_id)->where('from_id', $task->performer_id)->delete();
        $performer = User::query()->find($task->performer_id);
        if ((int)$request->good === 1) {
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
        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'performer_id' => $task->performer_id,
            'task_id' => $task->id,
            'name_task' => $task->name,
            'description' => 1,
            'type' => Notification::SEND_REVIEW
        ]);
        NotificationService::sendNotificationRequest($task->performer_id, $notification);

        return $notification;
    }


    /**
     * task performer review
     * @param $task
     * @param $request
     * @return Notification
     * @throws JsonException
     */
    public static function performerReview($task, $request): Notification
    {
        Review::query()->create([
            'description' => $request->comment,
            'good_bad' => $request->good,
            'task_id' => $task->id,
            'reviewer_id' => $task->performer_id,
            'user_id' => $task->user_id,
            'as_performer' => 1
        ]);
        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'performer_id' => $task->performer_id,
            'task_id' => $task->id,
            'name_task' => $task->name,
            'description' => 1,
            'type' => Notification::SEND_REVIEW_PERFORMER
        ]);
        NotificationService::sendNotificationRequest($task->user_id, $notification);

        $user = User::query()->find($task->user_id);
        if ((int)$request->good === 1) {
            $user->increment('review_good');
        } else {
            $user->increment('review_bad');
        }
        $user->increment('reviews');
        $task->performer_review = 1;
        $task->save();

        return $notification;
    }

    /**
     * Taskka review qoldirish
     * @param $task
     * @param $request
     * @param bool $status
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public static function sendReview($task, $request, bool $status = false): void
    {
        switch (true) {
            case $task->user_id === auth()->id() :
                // user review to performer
                $locale = (new CustomService)->cacheLang($task->performer_id);
                if ($status) {
                    $request['status'] = 1;
                }
                $notification = self::userReview($task, $request);
                NotificationService::pushNotification($task->performer, [
                    'title' => __('Новый отзыв', [], $locale), 'body' => __('О вас оставлен новый отзыв', [], $locale) . " \"$task->name\" №$task->id"
                ], 'notification', (new NotificationService)->notificationResource($notification));
                break;
            case $task->performer_id === auth()->id() :
                // performer review to user
                $locale = (new CustomService)->cacheLang($task->user_id);
                $notification = self::performerReview($task, $request);
                NotificationService::pushNotification($task->user, [
                    'title' => __('Новый отзыв', [], $locale), 'body' => __('О вас оставлен новый отзыв', [], $locale) . " \"$task->name\" №$task->id"
                ], 'notification', (new NotificationService)->notificationResource($notification));
                break;
        }
    }
}
