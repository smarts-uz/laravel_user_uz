<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReviewRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\TaskIndexResource;
use App\Models\ChMessage;
use App\Models\Task;
use App\Services\Task\CreateService;
use App\Services\Task\ReviewService;
use App\Services\Task\UpdateTaskService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class UpdateAPIController extends Controller
{
    protected CreateService $service;

    public function __construct()
    {
        $this->service = new CreateService();
    }

    public function __invoke(UpdateRequest $request, Task $task): JsonResponse
    {
        (new UpdateTaskService)->taskGuard($task);
        $data = $request->validated();
        $data = (new UpdateTaskService)->getAddress($data);
        $task->update($data);
        $this->service->syncCustomFields($task);

        Alert::success('Success');
        return response()->json(['message' => 'Success']);
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
    public function completed(Task $task): JsonResponse
    {
        (new UpdateTaskService)->taskGuardApi($task);
        $data = [
            'status' => Task::STATUS_COMPLETE
        ];
        ChMessage::query()->where('from_id', $task->user_id)->where('to_id', $task->performer_id)->delete();
        ChMessage::query()->where('to_id', $task->user_id)->where('from_id', $task->performer_id)->delete();

        $task->update($data);
        return response()->json([
            'success' => true,
            'message' => __('Успешно сохранено'),
            'task' => new TaskIndexResource($task)
        ]);
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
    public function not_completed(Request $request, Task $task)
    {
        (new UpdateTaskService)->taskGuardApi($task);
        $request->validate(['reason' => 'required'], ['reason.required' => 'Reason is required']);

        ChMessage::query()->where('from_id', $task->user_id)->where('to_id', $task->performer_id)->delete();
        ChMessage::query()->where('to_id', $task->user_id)->where('from_id', $task->performer_id)->delete();

        $task->update(['status' => Task::STATUS_NOT_COMPLETED, 'not_completed_reason' => $request->get('reason')]);
        return response()->json([
            'success' => true,
            'message' => __('Успешно сохранено')
        ]);
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
