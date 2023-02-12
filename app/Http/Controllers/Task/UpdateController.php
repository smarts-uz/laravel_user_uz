<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Services\Task\CreateService;
use App\Services\Task\UpdateTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    protected CreateService $service;

    public $updateTask;

    public function __construct(UpdateTaskService $updateTaskService, CreateService $createService)
    {
        $this->updateTask = $updateTaskService;
        $this->service = $createService;
    }

    /**
     *
     * Function  change
     * @param UpdateRequest $request
     * @param int $task_id
     * @return  RedirectResponse
     */
    public function change(UpdateRequest $request, int $task_id): RedirectResponse
    {
        $this->updateTask->change($task_id, $request);
        return redirect()->route('searchTask.task', $task_id);
    }

    /**
     *
     * Function  deleteImage
     * @param Request $request
     * @param int $task_id
     * @return  bool
     */
    public function deleteImage(Request $request, int $task_id): bool
    {
        $img = $request->get('image');
        $this->updateTask->deleteImage2($task_id, $img);
        return true;
    }

    /**
     *
     * Function  completed
     * @param int $task_id
     * @return  JsonResponse|RedirectResponse
     */
    public function completed(int $task_id)
    {
        return $this->updateTask->completed($task_id);
    }

    /**
     *
     * Function  not_completed
     * @param Request $request
     * @param int $task_id
     * @return  mixed
     */
    public function not_completed(Request $request, int $task_id)
    {
        $request->validate(['reason' => 'required']);
        $data = $request->get('reason');
        return $this->updatetask->not_completed($task_id, $data);
    }

    /**
     *
     * Function  sendReview
     * @param int $task_id
     * @param Request $request
     * @return  mixed
     */
    public function sendReview(int $task_id, Request $request)
    {
        return $this->updatetask->sendReview($task_id, $request);
    }
}
