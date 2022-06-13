<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\TaskIndexResource;
use App\Models\Chat\ChMessage;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Models\Review;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Task\ReviewService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\Task\CreateService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class UpdateAPIController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CreateService();
    }

    public function __invoke(UpdateRequest $request, Task $task)
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
    public function completed(Task $task)
    {
        taskGuard($task);
        $data = [
            'status' => Task::STATUS_COMPLETE
        ];
        ChMessage::query()->where('from_id', $task->user_id)->where('to_id', $task->performer_id)->delete();
        ChMessage::query()->where('to_id', $task->user_id)->where('from_id', $task->performer_id)->delete();

        $task->update($data);
        return response()->json(['message' => 'Success', 'success' => true, 'task' => new TaskIndexResource($task)]);
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
    public function sendReview(Task $task, Request $request)
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
                NotificationService::pushNotification($task->user->firebase_token, [
                    'title' => 'New review', 'body' => 'See details'
                ], 'notification', new NotificationResource($notification));

            } elseif ($task->performer_id == auth()->id()) {

                $notification = ReviewService::performerReview($task, $request);
                NotificationService::pushNotification($task->user->firebase_token, [
                    'title' => 'New review', 'body' => 'See details'
                ], 'notification', new NotificationResource($notification));

            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => "fail"]);  //back();
        }
        DB::commit();
        return response()->json(['success' => true, 'message' => " success"]);  //back();
    }


}
