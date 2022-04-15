<?php


namespace App\Services;


use App\Events\MyEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public static function sendTaskNotification($task)
    {
        $users = User::query()->pluck('category_id', 'id')->toArray();
        $user_ids = [];
        foreach ($users as $user_id => $category_id) {
            $user_cat_ids = explode(",", $category_id);
            $check_for_true = array_search($task->category_id, $user_cat_ids);
            if ($check_for_true !== false) {
                $user_ids[] = $user_id;

                Notification::create([
                    'user_id' => $user_id,
                    'description' => 1,
                    'task_id' => $task->id,
                    "cat_id" => $task->category_id,
                    "name_task" => $task->name,
                    "type" => Notification::TASK_CREATED
                ]);
            }
        }

        Http::post('ws.smarts.uz/api/send-notification', [
            'user_ids' => $user_ids,
            'project' => 'user',
            'data' => ['url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently']
        ]);
    }

    public static function sendNotification($not, $slug)
    {
        if ($slug == 'news-notifications'){
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
                'description' => $not->message,
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
            'description' => $task->desciption,
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
}
