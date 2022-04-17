<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\TaskIndexResource;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Review;
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

    public function completed(Task $task){
        taskGuard($task);
        $data = [
            'status' => Task::STATUS_COMPLETE
        ];
        $task->update($data);

        return response()->json(['message'=> 'Success', 'success' => true, 'task' => new TaskIndexResource($task)]);

    }


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
            $review = new Review();
            $review->description = $request->comment;
            $review->good_bad = $request->good;
            $review->task_id = $task->id;
            $review->reviewer_id = auth()->id();
            $review->user_id = $task->performer_id;
            Notification::create([
                'user_id' => $task->user_id,
                'task_id' => $task->id,
                'name_task' => $task->name,
                'description' => 1,
                'type' => Notification::SEND_REVIEW
            ]);
            $review->save();
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['success' => false, 'message' =>"fail"]);  //back();
        }
        DB::commit();
        return response()->json(['success' => true, 'message' =>" success"]);  //back();
    }



}
