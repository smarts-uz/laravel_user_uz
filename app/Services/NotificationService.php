<?php


namespace App\Services;


use App\Events\MyEvent;
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
                        $query->where('user_id', '=', $user->id)->where('type', '=', 5);
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

                Notification::query()->create([
                    'user_id' => $user_id,
                    'performer_id' => $performer_id,
                    'description' => 'description',
                    'task_id' => $task->id,
                    "cat_id" => $task->category_id,
                    "name_task" => $task->name,
                    "type" => Notification::TASK_CREATED
                ]);
                $performer = User::query()->find($performer_id);
                if ($performer->sms_notification) {
                    (new SmsService())->send($performer->phone_number, 'Novoe zadaniye dlya vas, uspeyte otliknutsya.');
                }
                if ($performer->email_notification) {
                    Mail::to($performer->email)->send(new MessageEmail('Novoe zadaniye dlya vas, uspeyte otliknutsya.'));
                }
            }
        }

        Http::post('ws.smarts.uz/api/send-notification', [
            'user_ids' => $performer_ids,
            'project' => 'user',
            'data' => ['url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently']
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

        $user_ids = User::query()->where($column, 1)->pluck('id')->toArray();
        foreach ($user_ids as $user_id) {
            Notification::create([
                'user_id' => $user_id,
                'description' => $not->message ?? 'description',
                "name_task" => $not->title,
                "type" => $type
            ]);
        }

        Http::post('ws.smarts.uz/api/send-notification', [
            'user_ids' => $user_ids,
            'project' => 'user',
            'data' => ['url' => $slug . '/' . $not->id, 'name' => $not->title, 'time' => 'recently']
        ]);
    }

    public static function sendNotificationRequest($user_ids, $data)
    {
        return Http::post('ws.smarts.uz/api/send-notification', [
            'user_ids' => $user_ids,
            'project' => 'user',
            'data' => $data
        ]);
    }

    public static function sendTaskSelectedNotification($task)
    {
        $user_id = auth()->id();

        Notification::query()->create([
            'user_id' => $user_id,
            'description' => $task->desciption ?? 'description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => Notification::TASK_SELECTED
        ]);

        return Http::post('ws.smarts.uz/api/send-notification', [
            'user_ids' => [$user_id],
            'project' => 'user',
            'data' => ['url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently']
        ]);
    }

    public function selectPerformer()
    {

    }
}
