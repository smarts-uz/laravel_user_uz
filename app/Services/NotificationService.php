<?php


namespace App\Services;


use App\Events\MyEvent;
use App\Events\SendNotificationEvent;
use App\Http\Resources\NotificationResource;
use App\Mail\MessageEmail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PlayMobile\SMS\SmsService;

class NotificationService
{
    public static function getNotifications($user)
    {
        return Notification::with('user:id,name')
            ->where('is_read', 0)
            ->where(function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('performer_id', '=', $user->id)
                        ->whereIn('type', [4, 6, 7]);
                })
                    ->orWhere(function ($query) use ($user) {
                        $query->where('user_id', '=', $user->id)->whereIn('type', [5, 8]);
                    });
                if ($user->role_id == 2)
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
            ->limit(10)->get();
    }

    public static function sendTaskNotification($task, $user_id)
    {
        $performers = User::query()->where('role_id', 2)->pluck('category_id', 'id')->toArray();
        $performer_ids = [];
        foreach ($performers as $performer_id => $category_id) {
            $user_cat_ids = explode(",", $category_id);
            $check_for_true = array_search($task->category_id, $user_cat_ids);
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
                $performer = User::query()->find($performer_id);
                self::pushNotification($performer->firebase_token, [
                    'title' => 'New task',
                    'body' => "\"$task->name\": with budget for $task->budget sum"
                ], 'notification', new NotificationResource($notification));

                if ($performer->sms_notification) {
                    (new SmsService())->send($performer->phone_number, 'Novoe zadaniye dlya vas, uspeyte otliknutsya.');
                }
                if ($performer->email_notification) {
                    Mail::to($performer->email)->send(new MessageEmail('Novoe zadaniye dlya vas, uspeyte otliknutsya.'));
                }
            }
        }

        self::sendNotificationRequest($performer_ids, [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);
    }

    public static function sendNotification($not, $slug)
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
            $notification = Notification::create([
                'user_id' => $user_id,
                'description' => $not->message ?? 'description',
                "name_task" => $not->title,
                "type" => $type
            ]);

            self::pushNotification($token, [
                'title' => 'Task selected',
                'body' => 'See details'
            ], 'notification', new NotificationResource($notification));
        }

        self::sendNotificationRequest($user_ids, [
            'url' => $slug . '/' . $not->id, 'name' => $not->title, 'time' => 'recently'
        ]);

    }

    public static function sendNotificationRequest($user_ids, $data)
    {
        foreach ($user_ids as $user_id) {
            broadcast(
                new SendNotificationEvent(json_encode($data, $assoc = true), $user_id)
            )->toOthers();
        }
    }

    public static function sendTaskSelectedNotification($task)
    {
        $user = auth()->user();
        $notification = Notification::query()->create([
            'user_id' => $user->id,
            'description' => $task->desciption ?? 'description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => Notification::TASK_SELECTED
        ]);

        self::sendNotificationRequest([$user->id], [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);

        self::pushNotification($user->firebase_token, [
            'title' => 'Task selected',
            'body' => 'See details'
        ], 'notification', new NotificationResource($notification));
        return true;
    }

    public static function pushNotification($user_token, $notification, $type, $model)
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
