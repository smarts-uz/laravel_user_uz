<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\ChMessage;
use App\Models\Task;
use App\Services\Task\CreateService;
use App\Services\Task\ReviewService;
use App\Services\Task\UpdateTaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;

class UpdateController extends Controller
{
    protected CreateService $service;

    public $updateTask;

    public function __construct()
    {
        $this->updateTask = new UpdateTaskService;
        $this->service = new CreateService();
    }

    public function change(UpdateRequest $request, $task_id)
    {
        $this->updateTask->change($task_id, $request);
        return redirect()->route('searchTask.task', $task_id);
    }

    public function deleteImage(Request $request, $task_id)
    {
        $img = $request->get('image');
        $this->updateTask->deleteImage2($task_id, $img);
        return true;
    }

    public function completed($task_id)
    {
        return $this->updateTask->completed($task_id);
    }

    public function not_completed(Request $request, $task_id)
    {
        $request->validate(['reason' => 'required']);
        $data = $request->get('reason');
        return $this->updatetask->not_completed($task_id, $data);
    }

    public function sendReview($task_id, Request $request)
    {
        return $this->updatetask->sendReview($task_id, $request);
    }
}
