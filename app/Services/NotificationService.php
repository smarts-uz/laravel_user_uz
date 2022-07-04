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
     * Get notifications for web and api.
     * If that function used for api, Notification::TASK_CREATED type should be invisible
     *
     * @param $user // User model object
     * @param bool $web
     * @return Collection
     */
    public static function getNotifications($user, bool $web = true): Collection
    {
        return Notification::with('user:id,name', 'task:id,budget')
            ->whereIn('is_read', $web ? [0] : [0, 1])
            ->where(function ($query) use ($user, $web) {
                $query->where(function ($query) use ($user) {
                    $query->where('performer_id', '=', $user->id)
                        ->whereIn('type', [4, 6, 7]);
                })
                    ->orWhere(function ($query) use ($user) {
                        $query->where('user_id', '=', $user->id)->whereIn('type', [5, 8]);
                    });
                if ($user->role_id == 2 && $web)
                    $query->orWhere(function ($query) use ($user) {
                        $query->where('performer_id', '=', $user->id)->where('type', '=', 1);
                    });
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
        $performers = User::query()->where('role_id', 2)->select('id', 'category_id', 'firebase_token')->get();
        $performer_ids = [];
        foreach ($performers as $performer) {
            $user_cat_ids = explode(",", $performer->category_id);
            $notification = new Notification([
                'user_id' => $user_id,
                'performer_id' => $performer->id,
                'description' => 'description',
                'task_id' => $task->id,
                "cat_id" => $task->category_id,
                "name_task" => $task->name,
                "type" => Notification::TASK_CREATED
            ]);
            $price = number_format($task->budget, 0, '.', ' ');
            self::pushNotification($performer->firebase_token, [
                'title' => __('Новая задания'),
                'body' => __('task_name  №task_id с бюджетом до task_budget', [
                    'task_name' => $task->name, 'task_id' => $task->id, 'budget' => $price])
            ], 'notification', new NotificationResource($notification));

            if (in_array($task->category_id, $user_cat_ids)) {
                $performer_ids[] = $performer->id;

                $notification->save();
                $message = __('Новая задания') . "\n" . __('task_name  №task_id с бюджетом до task_budget', [
                        'task_name' => $task->name, 'task_id' => $task->id, 'budget' => $price
                    ]);
                if ($performer->sms_notification) {
                    (new SmsService())->send($performer->phone_number, $message);
                }
                if ($performer->email_notification) {
                    Mail::to($performer->email)->send(new MessageEmail($message));
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
        $user_ids = User::query()->where('news_notification', 1)->pluck('firebase_token', 'id')->toArray();
        foreach ($user_ids as $user_id => $token) {
            $notification = Notification::query()->create([
                'user_id' => $user_id,
                'description' => $not->message ?? 'description',
                "name_task" => $not->title,
                "type" => Notification::NEWS_NOTIFICATION
            ]);

            self::pushNotification($token, [
                'title' => __('Новости'),
                'body' => __('Важные новости и объявления для вас')
            ], 'notification', new NotificationResource($notification));
        }

        self::sendNotificationRequest(array_keys($user_ids), [
            'url' => $slug . '/' . $not->id, 'name' => $not->title, 'time' => 'recently'
        ]);
    }

    /**
     * Function for use send websocket notification by user ids
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
    public static function sendResponseToTaskNotification($task): bool
    {
        /** @var User $user */
        $user = auth()->user();
        $notification = Notification::query()->create([
            'user_id' => $user->id,
            'description' => $task->desciption ?? 'task description',
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
            'body' => __('task_name №task_id отправлен', ['task_name' => $task->name, 'task_id' => $task->id])
        ], 'notification', new NotificationResource($notification));

        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'performer_id' => $user->id,
            'description' => $task->desciption ?? 'task description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => Notification::RESPONSE_TO_TASK_FOR_USER
        ]);

        self::sendNotificationRequest([$task->user_id], [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);

        self::pushNotification($task->user->firebase_token, [
            'title' => __('Новый отклик'),
            'body' => __('performer откликнулся на задания task_name', [
                'performer' => $user->name, 'task_name' => $task->name
            ])
        ], 'notification', new NotificationResource($notification));

        return true;
    }

    public static function sendBalanceReplenished($user_id, $amount, $payment_system)
    {
        /** @var User $user */
        $user = User::query()->find($user_id);
        $sms_service = new SmsMobileService();
        $text = "Your balance is replenished to $amount sum by $payment_system";
        $sms_service->sms_packages($user->phone_number, $text);
        Mail::to($user->email)->send(new MessageEmail($text));
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
                    "data" => $model,
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                ],
                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
            ]
        )->body();
    }
}
