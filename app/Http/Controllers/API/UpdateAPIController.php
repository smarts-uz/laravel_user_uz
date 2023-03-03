<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReviewRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\Task;
use App\Services\Task\CreateService;
use App\Services\Task\ReviewService;
use App\Services\Task\UpdateTaskService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateAPIController extends Controller
{
    protected CreateService $service;
    public UpdateTaskService $updatetask;

    public function __construct(CreateService $createService,UpdateTaskService $updateTaskService){
        $this->service = $createService;
        $this->updatetask = $updateTaskService;
    }

    public function __invoke(UpdateRequest $request, int $task_id): JsonResponse
    {
        $data = $request->validated();
        return $this->updatetask->__invoke($task_id, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/task/{task}/complete",
     *     tags={"Responses"},
     *     summary="Complete Task",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="string"
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

    public function completed(int $task_id): JsonResponse
    {
        return $this->updatetask->completed($task_id);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks/{task}/not-complete",
     *     tags={"Task"},
     *     summary="Task status not complete",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="reason",
     *                    description="Reason for cancel task",
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

    public function not_completed(Request $request, int $task_id): JsonResponse
    {
        $request->validate(['reason' => 'required'], ['reason.required' => 'Reason is required']);
        $data = $request->get('reason');
        return $this->updatetask->not_completed($task_id, $data);
    }


    /**
     * @OA\Post(
     *     path="/api/send-review-user/{task}",
     *     tags={"Responses"},
     *     summary="Complete task",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="comment",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="good",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="status",
     *                    type="integer",
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

    public function sendReview(Task $task, ReviewRequest $request): JsonResponse
    {
        (new UpdateTaskService)->taskGuard($task);
        DB::beginTransaction();

        try {
            ReviewService::sendReview($task, $request);
        } catch (Exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('Не удалось отправить')]);  //back();
        }
        DB::commit();
        return response()->json(['success' => true, 'message' => __('Успешно отправлено')]);  //back();
    }
}
