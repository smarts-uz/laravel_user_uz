<?php


namespace App\Services;


use App\Events\SendNotificationEvent;
use App\Http\Resources\NotificationResource;
use App\Mail\MessageEmail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PlayMobile\SMS\SmsService;

class NotificationService
{

    /**
     * Send new task notification by websocket, sms and firebase
     *
     * @param $user // User model object
     * @param array $is_read
     * @return Collection
     */
    public static function getNotifications($user, array $is_read = [0]): Collection
    {
        return Notification::with('user:id,name')
            ->whereIn('is_read', $is_read)
            ->where(function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('performer_id', '=', $user->id)
                        ->whereIn('type', [4, 6, 7]);
                })
                    ->orWhere(function ($query) use ($user) {
                        $query->where('user_id', '=', $user->id)->whereIn('type', [5, 8]);
                    });
                // appda new task notificationlar listda korinmasligi kerak
//                if ($user->role_id == 2)
//                    $query->orWhere(function ($query) use ($user) {
//                        $query->where('performer_id', '=', $user->id)->where('type', '=', 1);
//                    });
                if ($user->system_notification)
                    $query->orWhere(function ($query) use ($user) {
                        $query->where('user_id', '=', $user->id)->where('type', '=', 2);
                    });
                if ($user->news_notification)
                    $query->orWhere(function ($query) use ($user) {
                        $query->where('user_id', '=', $user->id)->where('type', '=', 3);
                    });
            })
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Send new task notification by websocket, sms and firebase
     *
     * @param $task // Task model object
     * @param  $user_id
     * @return void
     */
    public static function sendTaskNotification($task, $user_id): void
    {
        $performers = User::query()->where('role_id', 2)->pluck('category_id', 'id')->toArray();
        $performer_ids = [];
        foreach ($performers as $performer_id => $category_id) {
            $user_cat_ids = explode(",", $category_id);
            $check_for_true = in_array($task->category_id, $user_cat_ids);
            if ($check_for_true !== false) {
                $performer_ids[] = $performer_id;

                $notification = Notification::query()->create([
                    'user_id' => $user_id,
                    'performer_id' => $performer_id,
                    'description' => 'description',
                    'task_id' => $task->id,
                    "cat_id" => $task->category_id,
                    "name_task" => $task->name,
                    "type" => Notification::TASK_CREATED
                ]);
                /** @var User $performer */
                $performer = User::query()->find($performer_id);
                $price = number_format($task->budget, 0, '.', ' ');
                self::pushNotification($performer->firebase_token, [
                    'title' => __('Новая задания'),
                    'body' => __('task_name  №task_id с бюджетом до task_budget', [
                        'task_name' => $task->name, 'task_id' => $task->id, 'budget' => $price])
                ], 'notification', new NotificationResource($notification));

                if ($performer->sms_notification) {
                    (new SmsService())->send($performer->phone_number, (__('Новое задание для вас, успейте откликнуться.')));
                }
                if ($performer->email_notification) {
                    Mail::to($performer->email)->send(new MessageEmail(__('Новое задание для вас, успейте откликнуться.')));
                }
            }
        }

        self::sendNotificationRequest($performer_ids, [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);
    }


    /**
     * Send news, system notifications by websocket and firebase
     *
     * @param $not // Notification model object
     * @param  $slug
     * @return void
     */
    public static function sendNotification($not, $slug): void
    {
        if ($slug == 'news-notifications') {
            $type = Notification::NEWS_NOTIFICATION;
            $column = 'news_notification';
        } else {
            $type = Notification::SYSTEM_NOTIFICATION;
            $column = 'system_notification';
        }

        $user_ids = User::query()->where($column, 1)->pluck('firebase_token',   'id')->toArray();
        foreach ($user_ids as $user_id => $token) {
            $notification = Notification::query()->create([
                'user_id' => $user_id,
                'description' => $not->message ?? 'description',
                "name_task" => $not->title,
                "type" => $type
            ]);

            self::pushNotification($token, [
                'title' => $not->title,
                'body' =>  $not->message ?? __('Смотрите подробности')
            ], 'notification', new NotificationResource($notification));
        }

        self::sendNotificationRequest($user_ids, [
            'url' => $slug . '/' . $not->id, 'name' => $not->title, 'time' => 'recently'
        ]);

    }

    /**
     * Send news, system notifications by websocket and firebase
     *
     * @param $user_ids // User ids array
     * @param  $data
     * @return void
     */
    public static function sendNotificationRequest($user_ids, $data): void
    {
        foreach ($user_ids as $user_id) {
            broadcast(
                new SendNotificationEvent(json_encode($data, $assoc = true), $user_id)
            )->toOthers();
        }
    }

    /**
     * Send notification when performer response to task by websocket and firebase
     *
     * @param $task // Task model object
     * @return bool
     */
    public static function sendTaskSelectedNotification($task): bool
    {
        /** @var User $user */
        $user = auth()->user();
        $notification = Notification::query()->create([
            'user_id' => $user->id,
            'description' => $task->desciption ?? 'description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => Notification::RESPONSE_TO_TASK
        ]);

        self::sendNotificationRequest([$user->id], [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);

        self::pushNotification($user->firebase_token, [
            'title' => __('Отклик к заданию'),
            'body' => __('Отклик к заданию task_name №task_id отправлен', ['task_name' => $task->name, 'task_id' => $task->id])
        ], 'notification', new NotificationResource($notification));
        return true;
    }

    /**
     * Function for use send push(firebase) notifications
     *
     * @param $user_token // User firebase token
     * @param $notification // Notification title and body
     * @param $type // for notification or chat. Values - e.g. "chat", "notification"
     * @param $model // data for handling in mobile
     * @return string
     */
    public static function pushNotification($user_token, $notification, $type, $model): string
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'key=' . env('FCM_SERVER_KEY')
        ])->post('https://fcm.googleapis.com/fcm/send',
            [
                "to" => $user_token,
                "notification" => $notification,
                "data" => [
                    "type" => $type,
                    "data" => $model
                ]
            ]
        )->body();
    }
}
