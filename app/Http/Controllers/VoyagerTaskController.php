<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\Task\CreateService;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        $tasks = Task::query()->where('status',Task::STATUS_COMPLETE_WITHOUT_REVIEWS)->with(['reviews','user','performer'])
            ->whereHas('user')
            ->whereHas('performer')
            ->whereHas('reviews')
            ->paginate(60);
        return view('task.reported-tasks',compact('tasks'));
    }


    public function complete_task(Request $request, Task $task)
    {
        abort_if(!auth()->user()->hasPermission('reported_task_complete'),403);
        $task->status = Task::STATUS_COMPLETE;
        $task->save();
        return redirect()->route('admin.tasks.reported');
    }

    public function delete_task(Task $task)
    {
        abort_if(!auth()->user()->hasPermission('delete_tasks'),403);
        $this->service->delete($task);
        return redirect()->route('admin.tasks.reported');
    }
}
