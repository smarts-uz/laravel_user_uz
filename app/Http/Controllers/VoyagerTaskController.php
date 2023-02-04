<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Services\Task\CreateService;
use App\Services\TaskNotificationService;
use App\Services\UserNotificationService;


class VoyagerTaskController extends Controller
{
    protected CreateService $service;

    public function __construct()
    {
        $this->service = new CreateService();
    }

    public function reported_tasks()
    {
        abort_if(!auth()->user()->hasPermission('reported_task_view'),403);
        $tasks = Task::query()->where('status',Task::STATUS_NOT_COMPLETED)->with(['reviews','user','performer'])
            ->whereHas('user')
            ->whereHas('performer')
            ->orderBy('updated_at', 'desc')
            ->paginate(60);
        return view('task.reported-tasks',compact('tasks'));
    }

    public function complete_task(Task $task)
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

    /**
     * @OA\Get(
     *     path="/api/test-complete-task/{task}",
     *     tags={"Notifications"},
     *     summary="complete task notifications",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function test_complete_task(Task $task)
    {
        if (auth()->user()->hasPermission('reported_task_complete')){
            $task->status = Task::STATUS_COMPLETE;
            $task->save();

            // Send notification when admin closes the task
            UserNotificationService::sendNotificationToUser($task, Notification::ADMIN_COMPLETE_TASK);

            UserNotificationService::sendNotificationToPerformer($task, Notification::ADMIN_COMPLETE_TASK);

            return ['success' => true];
        }
        return ['success' => false];
    }


    /**
     * @OA\Get(
     *     path="/api/test-delete-task/{task}",
     *     tags={"Notifications"},
     *     summary="delete task notifications",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function test_delete_task(Task $task)
    {

        if (auth()->user()->hasPermission('delete_tasks')){
            $task->update(['status' => Task::STATUS_CANCELLED]);
            TaskNotificationService::sendNotificationForCancelledTask($task);
            return ['success' => true];
        }

        return ['success' => false];
    }


    /**
     * @OA\Get(
     *     path="/api/test-cancel-task/{task}",
     *     tags={"Notifications"},
     *     summary="cencel task notifications",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function test_cancel_task(Task $task)
    {
        TaskNotificationService::sendNotificationForCancelledTask($task);
        $task->update(['status' => Task::STATUS_CANCELLED]);
        return ['success' => true];
    }
}
