<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Task\SearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class SearchAPIController extends Controller
{

    protected SearchService $search_service;

    public function __construct()
    {
        $this->search_service = new SearchService();
    }

    /**
     * @OA\Post(
     *     path="/api/cancel-task/{taskId}",
     *     tags={"Task"},
     *     summary="Cancel task",
     *     description="[**Telegram :** https://t.me/c/1334612640/138](https://t.me/c/1334612640/138).",
     *     @OA\Parameter(
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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
    public function cancelTask($taskId): JsonResponse
    {
        $authId = auth()->id();
        return $this->search_service->cancelTask($taskId, $authId);
    }

    /**
     * @OA\Delete(
     *     path="/api/delete-task/{taskId}/{userId}",
     *     tags={"Task"},
     *     summary="Delete Task",
     *     description="[**Telegram :** https://t.me/c/1334612640/139](https://t.me/c/1334612640/139).",
     *     security={
     *         {"token": {}}
     *     },
     *     @OA\Parameter(
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          in="path",
     *          description="foydalanuvchi idsi kiritiladi",
     *          name="userId",
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
    public function delete_task($taskId, $userId): JsonResponse
    {
      return $this->search_service->delete_task($taskId, $userId);
    }

    /**
     * @OA\Post(
     *     path="/api/task-cancel/{taskId}",
     *     tags={"Task"},
     *     summary="Task Cancel",
     *     description="[**Telegram :** https://t.me/c/1334612640/225](https://t.me/c/1334612640/225).",
     *     security={
     *         {"token": {}}
     *     },
     *     @OA\Parameter(
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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
    public function task_cancel($taskId): JsonResponse
    {
        $user = auth()->user();
        return $this->search_service->task_cancel($taskId, $user);
    }


    /**
     * @OA\Post(
     *     path="/api/favorite-task/create",
     *     tags={"Task"},
     *     summary="Favorite Task create",
     *     description="[**Telegram :** https://t.me/c/1334612640/261](https://t.me/c/1334612640/261).",
     *     security={
     *         {"token": {}}
     *     },
     *     @OA\Parameter(
     *          in="query",
     *          description="vazifa idsi kiritiladi",
     *          name="task_id",
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
    public function favorite_task_create(Request $request): JsonResponse
    {
        $task_id = $request['task_id'];
        $userId = auth()->id();
        return $this->search_service->favorite_task_create($task_id, $userId);
    }


    /**
     * @OA\Delete(
     *     path="/api/favorite-task/delete/{taskId}",
     *     tags={"Task"},
     *     summary="Favorite Task delete",
     *     description="[**Telegram :** https://t.me/c/1334612640/262](https://t.me/c/1334612640/262).",
     *     @OA\Parameter(
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     security={
     *         {"token": {}}
     *     },
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
    public function favorite_task_delete($taskId): JsonResponse
    {
        $userId = auth()->id();
        return $this->search_service->favorite_task_delete($taskId, $userId);
    }


    /**
     * @OA\Get(
     *     path="/api/favorite-task",
     *     tags={"Task"},
     *     summary="Favorite Task All",
     *     description="[**Telegram :** https://t.me/c/1334612640/263](https://t.me/c/1334612640/263).",
     *     security={
     *         {"token": {}}
     *     },
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
    public function favorite_task_all(): AnonymousResourceCollection
    {
        $userId = auth()->id();
        return $this->search_service->favorite_task_all($userId);
    }

}
