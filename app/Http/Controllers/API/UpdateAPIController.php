<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\TaskIndexResource;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Models\Review;
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

        return response()->json(['message'=> 'Success']); //redirect()->route('searchTask.task', $task->id);


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
    public function completed(Task $task){
        taskGuard($task);
        $data = [
            'status' => Task::STATUS_COMPLETE
        ];
        $task->update($data);

        return response()->json(['message'=> 'Success', 'success' => true, 'task' => new TaskIndexResource($task)]);

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
    public function sendReview(Task $task, Request $request){
        $request->validate([
            'comment' => 'required',
            'good' => 'required',
            'status' => 'required',
        ]);
        taskGuard($task);
        DB::beginTransaction();

        try {
            $task->status  =  $request->status ? Task::STATUS_COMPLETE: Task::STATUS_COMPLETE_WITHOUT_REVIEWS;
            $task->save();

        Review::create([
            'description' => $request->comment,
            'good_bad' => $request->good,
            'task_id' => $task->id,
            'reviewer_id' => auth()->id(),
            'user_id' => $task->performer_id,
        ]);

            Notification::create([
                'user_id' => $task->user_id,
                'task_id' => $task->id,
                'name_task' => $task->name,
                'description' => 1,
                'type' => Notification::SEND_REVIEW
            ]);

        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['success' => false, 'message' =>"fail"]);  //back();
        }
        DB::commit();
        return response()->json(['success' => true, 'message' =>" success"]);  //back();
    }



}
