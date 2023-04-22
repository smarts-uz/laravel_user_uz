<?php

namespace App\Services;

use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Mail\{VerifyEmail, MessageEmail};
use App\Models\{User, Notification, UserCategory};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\{Collection, Facades\Http, Facades\Mail};
use App\Events\SendNotificationEvent;
use App\Http\Resources\NotificationResource;

class NotificationService
{

    /**
     * Get notifications for web and api.
     * If that function used for api, Notification::TASK_CREATED type should be invisible
     *
     * @param $user // User model object
     * @return Collection
     */
    public static function getNotifications($user): Collection
    {
        return Notification::with('user:id,name', 'task:id,budget')
            ->whereIn('is_read', [0])
            ->where(function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('performer_id', '=', $user->id)
                        ->whereIn('type', [
                            Notification::GIVE_TASK, Notification::SEND_REVIEW, Notification::SELECT_PERFORMER,
                            Notification::CANCELLED_TASK, Notification::ADMIN_COMPLETE_TASK, Notification::ADMIN_CANCEL_TASK
                        ]);
                })
                    ->orWhere(function ($query) use ($user) {
                        $query->where('user_id', '=', $user->id)
                            ->whereIn('type', [
                                Notification::RESPONSE_TO_TASK, Notification::SEND_REVIEW_PERFORMER,
                                Notification::RESPONSE_TO_TASK_FOR_USER, Notification::CANCELLED_TASK,
                                Notification::ADMIN_COMPLETE_TASK, Notification::ADMIN_CANCEL_TASK,
                                Notification::NEW_PASSWORD, Notification::WALLET_BALANCE,Notification::TEST_PUSHER_NOTIFICATION
                            ]);
                    });
                if ((int)$user->role_id === User::ROLE_PERFORMER)
                    $query->orWhere(function ($query) use ($user) {
                        $query->where('performer_id', '=', $user->id)->where('type', '=', Notification::TASK_CREATED);
                    });
                if ($user->system_notification)
                    $query->orWhere(function ($query) use ($user) {
                        $query->where('user_id', '=', $user->id)->where('type', '=', Notification::NEWS_NOTIFICATION);
                    });
                if ($user->news_notification)
                    $query->orWhere(function ($query) use ($user) {
                        $query->where('user_id', '=', $user->id)->where('type', '=', Notification::SYSTEM_NOTIFICATION);
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public static function sendTaskNotification($task, $user_id): void
    {
        $user_categories = UserCategory::query()->where('category_id', $task->category_id)->pluck('user_id')->toArray();
        $performers = User::query()->whereIn('id', $user_categories)->select('id', 'email', 'firebase_token', 'sms_notification', 'email_notification', 'phone_number')->get();
        foreach ($performers as $performer) {
            /** @var Notification $notification */
            $notification = Notification::query()->create([
                'user_id' => $user_id,
                'performer_id' => $performer->id,
                'description' => 'description',
                'task_id' => $task->id,
                "cat_id" => $task->category_id,
                "name_task" => $task->name,
                "type" => Notification::TASK_CREATED
            ]);
            $locale = (new CustomService)->cacheLang($performer->id);
            $price = number_format($task->budget, 0, '.', ' ');
            self::pushNotification($performer, [
                'title' => self::titles($notification->type),
                'body' => self::descriptions($notification)
            ], 'notification', new NotificationResource($notification));

            self::sendNotificationRequest($performer->id,$notification);

            $subject = __('Новая задания', [], $locale);
            $message = $subject . "\n" . __('task_name  №task_id с бюджетом до task_budget', [
                    'task_name' => $task->name, 'task_id' => $task->id, 'budget' => $price
                ], $locale);
            if ($performer->sms_notification) {
                $phone_number = (new CustomService)->correctPhoneNumber($performer->phone_number);
                SmsMobileService::sms_packages($phone_number, $message);
            }

            if ($performer->email_notification) {
                $task_id = $task->id;
                Mail::to($performer->email)->send(new MessageEmail($task_id, $message, $subject));
            }
        }

    }


    /**
     * Send news, system notifications by websocket and firebase
     *
     * @param $not // Notification model object
     * @return void
     * @throws JsonException
     */
    public static function sendNotification($not): void
    {
        /** @var User $users */
        $users = User::query()->with('sessions')->where('news_notification', 1)->select('id')->get();
        foreach ($users as $user) {
            /** @var Notification $notification */
            $notification = Notification::query()->create([
                'user_id' => $user->id,
                'description' => $not->message ?? 'description',
                "name_task" => $not->title,
                "news_id" => $not->id,
                "type" => Notification::NEWS_NOTIFICATION
            ]);
            self::pushNotification($user, [
                'title' => self::titles($notification->type),
                'body' => self::descriptions($notification)
            ], 'notification', new NotificationResource($notification));
            self::sendNotificationRequest($user->id, $notification);
        }

    }

    /**
     * Function for use send websocket notification by user ids
     *
     * @param $user_id
     * @param Notification $notification
     * @return void
     * @throws JsonException
     */
    public static function sendNotificationRequest($user_id, Notification $notification): void
    {

        $data = [
            'created_date' => $notification->created_at->format('d M'),
            'title' => self::titles($notification->type),
            'url' => route('show_notification', [$notification]),
            'description' => self::descriptions($notification)
        ];

        $response = broadcast(
            new SendNotificationEvent(json_encode($data, JSON_THROW_ON_ERROR), $user_id)
        )->toOthers();

        $notification->response = $response;
        $notification->save();
    }

    /**
     * Send notification when performer response to task by websocket and firebase
     *
     * @param $task // Task model object
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public static function sendResponseToTaskNotification($task): bool
    {
        // 1. Send notification to responded performer
        /** @var User $user */
        $user = auth()->user();
        $locale = (new CustomService)->cacheLang($user->id);
        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $user->id,
            'description' => $task->desciption ?? 'task description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => Notification::RESPONSE_TO_TASK
        ]);

        self::sendNotificationRequest($user->id, $notification);

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

        $locale = (new CustomService)->cacheLang($task->user_id);

        self::sendNotificationRequest($task->user_id, $notification);

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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function sendBalanceReplenished($user_id, $amount, $payment_system, $transaction_id): void
    {
        /** @var User $user */
        $user = User::query()->find($user_id);
        $amount = number_format($amount, 0, '.', ' ');
        $message = __("Ваш баланс на сайте UserUz пополнен на сумму amount через payment_system. Номер транзакции = transaction_id ID пользователя = user_id", [
            'amount' => $amount, 'payment_system' => $payment_system, 'transaction_id' => $transaction_id, 'user_id' => $user_id
        ], (new CustomService)->cacheLang($user_id));
        $phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
        SmsMobileService::sms_packages($phone_number, $message);
        Mail::to($user->email)->send(new VerifyEmail($message));
    }


    /**
     * Bu method yangi ro'yxatdan o'tib kirganida unga balans
     * berilgani yoki parol o'rnatishi kerakligi haqida push bildirishnoma yuboradi
     * @param User $user
     * @param Notification $notification
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function pushNoti(User $user, Notification $notification): void
    {
        $locale = (new CustomService)->cacheLang($user->id);

        self::pushNotification($user, [
            'title' => self::titles($notification->type, $locale),
            'body' => self::descriptions($notification, $locale)
        ], 'notification', new NotificationResource($notification));

        $notification->status = 1;
        $notification->save();

    }

    /**
     * test firebase notification
     * @param $type
     * @param $title
     * @param $text
     * @param $user_id
     * @return array
     */
    public function firebase_notif($type, $title, $text, $user_id): array
    {
        $users = match ($type) {
            'all' => User::all(),
            'role_user' => User::query()->where('role_id', User::ROLE_USER)->get(),
            'role_performer' => User::query()->where('role_id', User::ROLE_PERFORMER)->get(),
            'role_admin' => User::query()->where('role_id', User::ROLE_ADMIN)->get(),
            default => null,
        };
        if ($users !== null && $user_id === null){
            foreach ($users as $user) {
                $notification = [
                    'user_id' => $user->id,
                    'description' => $text,
                    'type' => Notification::TEST_FIREBASE_NOTIFICATION
                ];
                self::pushNotification($user, [
                    'title' => $title,
                    'body' => $text
                ], 'notification', $notification);
            }
        }
        if ($user_id !== null && $users === null){
            $user = User::query()->findOrFail($user_id);
            $notification = [
                'user_id' => $user->id,
                'description' => $text,
                'type' => Notification::TEST_FIREBASE_NOTIFICATION
            ];
            self::pushNotification($user, [
                    'title' => $title,
                    'body' => $text
            ], 'notification', $notification);
        }

        return [
            'success' => true,
            'message' => 'success',
            'data' => [
                'type' => $type,
                'title' => $title,
                'text' => $text,
                'user_id' => $user_id
            ]
        ];
    }

    /**
     * test pusher notification
     * @param $type
     * @param $title
     * @param $text
     * @param $user_id
     * @return array
     * @throws JsonException
     */
    public function pusher_notif($type, $title, $text, $user_id): array
    {
        $users = match ($type) {
            'all' => User::all(),
            'role_user' => User::query()->where('role_id', User::ROLE_USER)->get(),
            'role_performer' => User::query()->where('role_id', User::ROLE_PERFORMER)->get(),
            'role_admin' => User::query()->where('role_id', User::ROLE_ADMIN)->get(),
            default => null,
        };
        $notification = [
            'created_date' => '29 Jan',
            'title' => $title,
            'url' => route('show_notification', [111232]),
            'description' => $text,
            'type'=> Notification::TEST_PUSHER_NOTIFICATION
        ];
        if ($users !== null && $user_id === null){
            foreach ($users as $user) {
                self::sendNotificationRequest($user->id, $notification);
            }
        }
        if ($user_id !== null && $users === null){
            $user = User::query()->findOrFail($user_id);
            self::sendNotificationRequest($user->id, $notification);
        }

        return [
            'success' => true,
            'message' => 'success',
            'data' => [
                'type' => $type,
                'title' => $title,
                'text' => $text,
                'user_id' => $user_id
            ]
        ];
    }

    /**
     * test sms notification
     * @param $type
     * @param $text
     * @param $user_id
     * @return array
     */
    public function sms_notif($type, $text, $user_id): array
    {
        $users = match ($type) {
            'all' => User::all(),
            'role_user' => User::query()->where('role_id', User::ROLE_USER)->get(),
            'role_performer' => User::query()->where('role_id', User::ROLE_PERFORMER)->get(),
            'role_admin' => User::query()->where('role_id', User::ROLE_ADMIN)->get(),
            default => null,
        };
        if ($users !== null && $user_id === null){
            foreach ($users as $user) {
                $phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
                SmsMobileService::sms_packages($phone_number, $text);
            }
        }
        if ($user_id !== null && $users === null){
            $user = User::query()->findOrFail($user_id);
            $phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
            SmsMobileService::sms_packages($phone_number, $text);
        }

        return [
            'success' => true,
            'message' => 'success',
            'data' => [
                'type' => $type,
                'text' => $text,
                'user_id' => $user_id
            ]
        ];
    }

    /**
     * test email notification
     * @param $type
     * @param $text
     * @param $user_id
     * @return array
     */
    public function email_notif($type, $text, $user_id): array
    {
        $users = match ($type) {
            'all' => User::all(),
            'role_user' => User::query()->where('role_id', User::ROLE_USER)->get(),
            'role_performer' => User::query()->where('role_id', User::ROLE_PERFORMER)->get(),
            'role_admin' => User::query()->where('role_id', User::ROLE_ADMIN)->get(),
            default => null,
        };
        if ($users !== null && $user_id === null){
            foreach ($users as $user) {
                Mail::to($user->email)->send(new VerifyEmail($text));
            }
        }
        if ($user_id !== null && $users === null){
            $user = User::query()->findOrFail($user_id);
            Mail::to($user->email)->send(new VerifyEmail($text));
        }

        return [
            'success' => true,
            'message' => 'success',
            'data' => [
               'type' => $type,
               'text' => $text,
               'user_id' => $user_id
            ]
        ];
    }

    /**
     * Test task create notification
     * @param $user_id
     * @param $task_id
     * @param $task_name
     * @param $task_category_id
     * @param $title
     * @param $body
     * @return array
     */
    #[ArrayShape(['success' => "bool", 'message' => "string", 'data' => "array"])]
    public function task_create_notification($user_id, $task_id, $task_name, $task_category_id, $title, $body): array
    {
        $user_cat_ids = UserCategory::query()->where('category_id', $task_category_id)->pluck('user_id')->toArray();
        $performers = User::query()->whereIn('id', $user_cat_ids)->get();
        foreach ($performers as $performer) {
            $notification = [
                'user_id' => $user_id,
                'performer_id' => $performer->id,
                'description' => $title,
                'task_id' => $task_id,
                "cat_id" => $task_category_id,
                "name_task" => $task_name,
                "type" => Notification::TASK_CREATED
            ];
            self::pushNotification($performer, [
                'title' => $title,
                'body' => $body,
            ], 'notification', $notification);
        }

        return [
            'success' => true,
            'message' => 'success',
            'data' => $notification
        ];
    }

    /**
     * Function for use send push(firebase) notifications
     *
     * @param $user // User model
     * @param $notification // Notification title and body
     * @param $type // for notification or chat. Values - e.g. "chat", "notification"
     * @param $model // data for handling in mobile
     */
    public static function pushNotification(User $user, $notification, $type, $model): void
    {
        $NowTime = Carbon::now()->format('H:i:s');
        $notification['sound'] = "default";
        foreach ($user->sessions as $session) {
            if ($user->notification_off !== 1 || ($NowTime < $user->notification_from && $NowTime > $user->notification_to)) {
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
                            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        ],
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                    ]
                )->body();

            }
        }
    }

    /**
     * bu method notification turiga qarab titleni qaytaradi
     * @param $type
     * @param $locale
     * @return string
     */
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
            Notification::NEW_PASSWORD => __('Установить пароль', [], $locale),
            Notification::WALLET_BALANCE => __('Дополнительный бонус', [], $locale),
            default => 'Title',
        };
    }

    /**
     * bu method notification turiga qarab descriptionni qaytaradi
     * @param $notification
     * @param $locale
     * @return string
     */
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
            Notification::NEW_PASSWORD => __('Чтобы не потерять доступ к вашему аккаунту, рекомендуем вам установить пароль. Сделать это можно в профиле, раздел "Настройки".', [], $locale),
            Notification::WALLET_BALANCE => __('USer.Uz предоставил вам бонус в размере default сумов.', [
                'default' => setting('admin.bonus',0)
            ], $locale),
            default => 'Description',
        };
    }

    /**
     * bu method barcha notificationlarni o'qilgan qiladi
     * @param $user_id
     * @return Builder
     */
    public static function readAllNotifications($user_id): Builder
    {
        $user_notify = Notification::query()->where('user_id', $user_id)->orWhere('performer_id', $user_id);
        $user_notify->update([
            'is_read' => 1
        ]);
        return $user_notify;
    }
}
