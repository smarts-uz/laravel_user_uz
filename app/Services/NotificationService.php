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
                    "type" => 1
                ]);
            }
        }

        $response = Http::post('ws.smarts.uz/api/send-notification', [
            'user_ids' => $user_ids,
            'project' => 'user',
            'data' => ['url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently']
        ]);

//        dd($response->json());
    }

    public static function sendNotification($not, $slug)
    {
        if ($slug == 'news-notifications'){
            $type = 2;
            $column = 'news_notification';
        } else {
            $type = 3;
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

        $response = Http::post('ws.smarts.uz/api/send-notification', [
            'user_ids' => $user_ids,
            'project' => 'user',
            'data' => ['url' => $slug . '/' . $not->id, 'name' => $not->title, 'time' => 'recently']
        ]);

//        dd($response->json());
    }
}
