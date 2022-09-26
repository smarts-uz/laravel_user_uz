<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use App\Services\Task\CreateService;
use App\Services\TaskNotificationService;
use App\Services\UserNotificationService;
use Illuminate\Http\Request;


class VoyagerTaskController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CreateService();
    }

    public function reported_tasks(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('reported_task_view'),403);
        $tasks = Task::query()->where('status',Task::STATUS_NOT_COMPLETED)->with(['reviews','user','performer'])
            ->whereHas('user')
            ->whereHas('performer')
            ->orderBy('updated_at', 'desc')
            ->paginate(60);
        return view('task.reported-tasks',compact('tasks'));
    }


    public function complete_task(Request $request, Task $task)
    {
        abort_if(!auth()->user()->hasPermission('reported_task_complete'),403);
        $task->status = Task::STATUS_COMPLETE;
        $task->save();

        // Send notification when admin closes the task
        UserNotificationService::sendNotificationToUser($task, Notification::ADMIN_COMPLETE_TASK);

        UserNotificationService::sendNotificationToPerformer($task, Notification::ADMIN_COMPLETE_TASK);

        return redirect()->route('admin.tasks.reported');
    }

    public function delete_task(Task $task)
    {
        abort_if(!auth()->user()->hasPermission('delete_tasks'),403);
        $task->update(['status' => Task::STATUS_CANCELLED]);
        TaskNotificationService::sendNotificationForCancelledTask($task);
        return redirect()->route('admin.tasks.reported');
    }

    public function cancelTask(Task $task)
    {
        TaskNotificationService::sendNotificationForCancelledTask($task);
        $task->update(['status' => Task::STATUS_CANCELLED]);
        return redirect()->route('voyager.tasks.index');
    }
}
