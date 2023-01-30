<?php

namespace App\Services\Task;


use App\Http\Resources\TaskIndexResource;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    public function taskIncrement($user_id, $task_id) {
        $viewed_tasks = Cache::get('user_viewed_tasks' . $user_id) ?? [];
        if (!in_array($task_id, $viewed_tasks)) {
            $viewed_tasks[] = $task_id;
        }
        Cache::put('user_viewed_tasks' . $user_id, $viewed_tasks);
        $task = Task::find($task_id);
        $task->increment('views');
    }

    public function taskIndex($task_id) {
        $task = Task::find($task_id);
        return new TaskIndexResource($task);
    }
}
