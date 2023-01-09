<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;


class SearchAPIController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/cancel-task/{task}",
     *     tags={"Task"},
     *     summary="Cancel task",
     *     @OA\Parameter(
     *          in="path",
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
    public function cancelTask(Task $task)
    {
        if ($task->user_id !== auth()->id()){
            return response()->json([
                'success' => false,
                "message" => __("Отсутствует разрешение")
            ], 403);
        }
        $task->status = Task::STATUS_CANCELLED;
        $task->save();
        return response()->json([
            'success' => true,
            'message' => __('Успешно отменено')
        ]);
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
     *          name="task",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          in="path",
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
    public function delete_task(Task $task,User $user)
    {
        if ($task->user_id !== auth()->id()){
            return response()->json([
                'success' => false,
                "message" => __("Отсутствует разрешение")
            ], 403);
        }
        $task->delete();

        $user->active_step = null;
        $user->active_task = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('Успешно удалено')
        ]);
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
    public function task_cancel(Task $task): \Illuminate\Http\JsonResponse
    {
        if ($task->user_id !== auth()->id()){
            return response()->json([
                'success' => false,
                "message" => __("Отсутствует разрешение")
            ], 403);
        }

        auth()->user()->active_task = $task;
        auth()->user()->save();

        return response()->json([
            'success' => true,
            'message' => __('Успешно удалено')
        ]);
    }

}
