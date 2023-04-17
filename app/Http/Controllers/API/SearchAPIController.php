<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Services\Task\SearchService;
use Illuminate\Http\JsonResponse;


class SearchAPIController extends Controller
{

    protected SearchService $search_service;

    public function __construct()
    {
        $this->search_service = new SearchService();
    }

    /**
     * @OA\Post(
     *     path="/api/cancel-task/{task}",
     *     tags={"Task"},
     *     summary="Cancel task",
     *     @OA\Parameter(
     *          in="path",
     *          description="task id kiritiladi",
     *          name="task",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
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
    public function cancelTask(Task $task): JsonResponse
    {
        $authId = auth()->id();
        return $this->search_service->cancelTask($task, $authId);
    }

    /**
     * @OA\Delete(
     *     path="/api/delete-task/{task}/{user}",
     *     tags={"Task"},
     *     summary="Delete Task",
     *     security={
     *         {"token": {}}
     *     },
     *     @OA\Parameter(
     *          in="path",
     *          description="task id kiritiladi",
     *          name="task",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          in="path",
     *          description="user id kiritiladi",
     *          name="user",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Response(
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
     *     )
     * )
     */
    public function delete_task(Task $task,User $user): JsonResponse
    {
      return $this->search_service->delete_task($task, $user);
    }

    /**
     * @OA\Post(
     *     path="/api/task-cancel/{task}",
     *     tags={"Task"},
     *     summary="Task Cancel",
     *     security={
     *         {"token": {}}
     *     },
     *     @OA\Parameter(
     *          in="path",
     *          description="task id kiritiladi",
     *          name="task",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Response(
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
     *     )
     * )
     */
    public function task_cancel(Task $task): JsonResponse
    {
        $user = auth()->user();
        return $this->search_service->task_cancel($task, $user);
    }

}
