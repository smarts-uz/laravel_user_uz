<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\TaskIndexResource;
use App\Models\Chat\ChMessage;
use App\Models\Task;
use App\Services\NotificationService;
use App\Services\Task\ReviewService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Task\CreateService;
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
        taskGuard($task);
        $data = $request->validated();
        $data = getAddress($data);
        $task->update($data);
        $this->service->syncCustomFields($task);
        Alert::success('Success');

        return response()->json(['message' => 'Success']); //redirect()->route('searchTask.task', $task->id);


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
        taskGuardApi($task);
        $data = [
            'status' => Task::STATUS_COMPLETE
        ];
        ChMessage::query()->where('from_id', $task->user_id)->where('to_id', $task->performer_id)->delete();
        ChMessage::query()->where('to_id', $task->user_id)->where('from_id', $task->performer_id)->delete();

        $task->update($data);
        return response()->json(['message' => 'Success', 'success' => true, 'task' => new TaskIndexResource($task)]);
    }

    public function not_completed(Request $request, Task $task)
    {
        taskGuardApi($task);

        $request->validate(['reason' => 'required'], ['reason.required' => 'Reason is required']);
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
     *                    type="boolean",
     *                 ),
     *                 @OA\Property (
     *                    property="status",
     *                    type="boolean",
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
    public function sendReview(Task $task, Request $request): JsonResponse
    {
        $request->validate([
            'comment' => 'required',
            'good' => 'required',
            'status' => 'required',
        ]);
        taskGuard($task);
        DB::beginTransaction();

        try {
            if ($task->user_id == auth()->id()) {

                $notification = ReviewService::userReview($task, $request);
                NotificationService::pushNotification($task->user, [
                    'title' => __('Новый отзыв'), 'body' => __('О вас оставлен новый отзыв') . " \"$task->name\" №$task->id"
                ], 'notification', new NotificationResource($notification));

            } elseif ($task->performer_id == auth()->id()) {

                $notification = ReviewService::performerReview($task, $request);
                NotificationService::pushNotification($task->user, [
                    'title' => __('Новый отзыв'), 'body' => __('О вас оставлен новый отзыв') . " \"$task->name\" №$task->id"
                ], 'notification', new NotificationResource($notification));

            }
        } catch (Exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => "fail"]);  //back();
        }
        DB::commit();
        return response()->json(['success' => true, 'message' => " success"]);  //back();
    }


}
