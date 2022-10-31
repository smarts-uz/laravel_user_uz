<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;


class SearchAPIController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/profile/cancel-task/{task}",
     *     tags={"Task"},
     *     summary="Cancel task",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="Task id",
     *                    type="string",
     *                 ),
     *             ),
     *         ),
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
     * @OA\DELETE(
     *     path="/api/delete-task/{task}",
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
     *              type="string"
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
    public function delete_task(Task $task)
    {
        if ($task->user_id !== auth()->id()){
            return response()->json([
                'success' => false,
                "message" => __("Отсутствует разрешение")
            ], 403);
        }
        $task->delete();
        return response()->json([
            'success' => true,
            'message' => __('Успешно удалено')
        ]);
    }

}
