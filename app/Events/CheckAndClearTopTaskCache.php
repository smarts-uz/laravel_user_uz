<?php

namespace App\Events;

use Illuminate\Support\Facades\Cache;

class CheckAndClearTopTaskCache
{
    public function handle(TaskEvent $event)
    {
        $updatedTasks = $event->getUser();
        $tasks = Cache::get('tasks', []);
        foreach($tasks as $task) {
            if($updatedTasks->id == $task->id) {
                Cache::forget('tasks');
                return;
            }
        }
    }
}
