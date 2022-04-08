<?php


namespace App\Services;


use App\Events\MyEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function sendTaskNotification($task)
    {
        $users = User::all();

        foreach ($users as $user) {
            $user_cat_ids = explode(",", $user->category_id);
            $check_for_true = array_search($task->category_id, $user_cat_ids);

            if ($check_for_true !== false) {
                Notification::create([
                    'user_id' => $user->id,
                    'description' => 1,
                    'task_id' => $task->id,
                    "cat_id" => $task->category_id,
                    "name_task" => $task->name,
                    "type" => 1
                ]);
            }
        }
        $user_id_fjs = NULL;
        $id_task = $task->id;
        $id_cat = $task->category_id;
        $title_task = $task->name;
        $type = 1;

//        event(new MyEvent($id_task, $id_cat, $title_task, $type, $user_id_fjs));
    }

    public static function sendNotification($not, $slug)
    {
        if ($slug == 'news-notifications'){
            $type = 2;
        } else {
            $type = 3;
        }

        $user_ids = User::query()->pluck('id')->toArray();
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
