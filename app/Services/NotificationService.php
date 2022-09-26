<?php


namespace App\Services;


use App\Models\User;
use App\Mail\MessageEmail;
use App\Models\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Events\SendNotificationEvent;
use App\Http\Resources\NotificationResource;

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
                        ->whereIn('type', [4, 6, 7, 10, 11, 12]);
                })
                    ->orWhere(function ($query) use ($user) {
                        $query->where('user_id', '=', $user->id)
                            ->whereIn('type', [5, 8, 9, 10, 11, 12]);
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
        $performers = User::query()->where('role_id', 2)->select('id', 'email', 'category_id', 'firebase_token', 'sms_notification', 'email_notification', 'phone_number')->get();
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
            $locale = cacheLang($performer->id);
            $price = number_format($task->budget, 0, '.', ' ');
            self::pushNotification($performer, [
                'title' => __('Новая задания', [], $locale),
                'body' => __('task_name  №task_id с бюджетом до task_budget', [
                    'task_name' => $task->name, 'task_id' => $task->id, 'budget' => $price], $locale)
            ], 'notification', new NotificationResource($notification));

            if (in_array($task->category_id, $user_cat_ids)) {
                $performer_ids[] = $performer->id;

                $notification->save();
                $message = __('Новая задания', [], $locale) . "\n" . __('task_name  №task_id с бюджетом до task_budget', [
                        'task_name' => $task->name, 'task_id' => $task->id, 'budget' => $price
                    ], $locale);
                if ($performer->sms_notification) {
                    $phone_number = $performer->phone_number;
                    SmsMobileService::sms_packages($phone_number, $message);
                }
                info(json_encode($performer));
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
        /** @var User $users */
        $users = User::query()->with('sessions')->where('news_notification', 1)->select('id')->get();
        foreach ($users as $user) {
            $notification = Notification::query()->create([
                'user_id' => $user->id,
                'description' => $not->message ?? 'description',
                "name_task" => $not->title,
                "type" => Notification::NEWS_NOTIFICATION
            ]);
            $locale = cacheLang($user->id);
            self::pushNotification($user, [
                'title' => __('Новости', [], $locale),
                'body' => __('Важные новости и объявления для вас', [], $locale)
            ], 'notification', new NotificationResource($notification));
        }

        self::sendNotificationRequest($users->map->id->toArray(), [
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
                new SendNotificationEvent(json_encode($data, true), $user_id)
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
        // 1. Send notification to responded performer
        /** @var User $user */
        $user = auth()->user();
        $locale = cacheLang($user->id);
        /** @var Notification $notification */
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

        self::pushNotification($user, [
            'title' => self::titles($notification->type, $locale),
            'body' => self::descriptions($notification, $locale)
        ], 'notification', new NotificationResource($notification));

        // 2. Send notification to task owner(user)
        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'performer_id' => $user->id,
            'description' => $task->desciption ?? 'task description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => Notification::RESPONSE_TO_TASK_FOR_USER
        ]);

        $locale = cacheLang($task->user_id);

        self::sendNotificationRequest([$task->user_id], [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);

        self::pushNotification($task->user, [
            'title' => self::titles($notification->type, $locale),
            'body' => self::descriptions($notification, $locale)
        ], 'notification', new NotificationResource($notification));

        return true;
    }

    /**
     * Send notification when balance replenished by payment systems (Click, Payme, Paynet)
     *
     * @param $user_id
     * @param $amount
     * @param $payment_system
     * @param $transaction_id
     * @return void
     */
    public static function sendBalanceReplenished($user_id, $amount, $payment_system, $transaction_id): void
    {
        /** @var User $user */
        $user = User::query()->find($user_id);
        $amount = number_format($amount, 0, '.', ' ');
        $message = __("Ваш баланс на сайте UserUz пополнен на сумму amount через payment_system. Номер транзакции = transaction_id ID пользователя = user_id", [
            'amount' => $amount, 'payment_system' => $payment_system, 'transaction_id' => $transaction_id, 'user_id' => $user_id
        ], cacheLang($user_id));
        $phone_number = $user->phone_number;
        SmsMobileService::sms_packages($phone_number, $message);
        Mail::to($user->email)->send(new MessageEmail($message));
    }

    /**
     * Function for use send push(firebase) notifications
     *
     * @param $user // User firebase token
     * @param $notification // Notification title and body
     * @param $type // for notification or chat. Values - e.g. "chat", "notification"
     * @param $model // data for handling in mobile
     */
    public static function pushNotification($user, $notification, $type, $model)
    {
        foreach ($user->sessions as $session) {
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'key=' . env('FCM_SERVER_KEY')
            ])->post('https://fcm.googleapis.com/fcm/send',
                [
                    "to" => $session->firebase_token,
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

    public static function titles($type, $locale = null): string
    {
        return match ($type) {
            Notification::TASK_CREATED => __('Новое задание', [], $locale),
            Notification::NEWS_NOTIFICATION, Notification::SYSTEM_NOTIFICATION => __('Новости', [], $locale),
            Notification::GIVE_TASK => __('Предложение', [], $locale),
            Notification::RESPONSE_TO_TASK => __('Отклик к заданию', [], $locale),
            Notification::SEND_REVIEW => __('Задание выполнено', [], $locale),
            Notification::SELECT_PERFORMER => __('Вас выбрали исполнителем', [], $locale),
            Notification::SEND_REVIEW_PERFORMER => __('Новый отзыв', [], $locale),
            Notification::RESPONSE_TO_TASK_FOR_USER => __('Новый отклик', [], $locale),
            Notification::CANCELLED_TASK, Notification::ADMIN_CANCEL_TASK => __('3адание отменено', [], $locale),
            Notification::ADMIN_COMPLETE_TASK => __('Задания завершено', [], $locale),
            default => 'Title',
        };
    }

    public static function descriptions($notification, $locale = null): string
    {
        return match ($notification->type) {
            Notification::TASK_CREATED => __('task_name  №task_id с бюджетом до task_budget', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
                'budget' => number_format($notification->task?->budget, 0, '.', ' ')
            ], $locale),
            Notification::NEWS_NOTIFICATION, Notification::SYSTEM_NOTIFICATION => __('Важные новости и объявления для вас', [], $locale),
            Notification::GIVE_TASK => __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
            ], $locale),
            Notification::RESPONSE_TO_TASK => __('task_name №task_id отправлен', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id
            ], $locale),
            Notification::SEND_REVIEW => __('Заказчик сказал, что вы выполнили эго задачу task_name №task_id и оставил вам отзыв', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
            ], $locale),
            Notification::SELECT_PERFORMER => __('Вас выбрали исполнителем  в задании task_name №task_id task_user', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
            ], $locale),
            Notification::SEND_REVIEW_PERFORMER => __('О вас оставлен новый отзыв', [], $locale) . " \"$notification->name_task\" №$notification->task_id",
            Notification::RESPONSE_TO_TASK_FOR_USER => __('performer откликнулся на задания task_name', [
                'performer' => $notification->performer?->name, 'task_name' => $notification->name_task
            ], $locale),
            Notification::CANCELLED_TASK => __('Ваше задание task_name №task_id было отменено', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
            ], $locale),
            Notification::ADMIN_COMPLETE_TASK => __('3адание task_name №task_id было завершено администрацией', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
            ], $locale),
            Notification::ADMIN_CANCEL_TASK => __('3адание task_name №task_id было отменено администрацией', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
            ], $locale),
            default => 'Description',
        };
    }
}
