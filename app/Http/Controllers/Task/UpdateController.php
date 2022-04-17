<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Review;
use Illuminate\Http\Request;
use App\Services\Task\CreateService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use RealRashid\SweetAlert\Facades\Alert;

class UpdateController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CreateService();
    }

    public function change(UpdateRequest $request, Task $task)
    {
        taskGuard($task);
        if ($task->responses_count)
            abort(403);

        $data = $request->validated();
        $data = getAddress($data);

        $task->update($data);
        $this->service->syncCustomFields($task);
        Alert::success('Success');

        return redirect()->route('searchTask.task', $task->id);


    }

    public function completed(Task $task){
        $data = [
            'status' => Task::STATUS_COMPLETE
        ];
        $task->update($data);

        Alert::success('Success');

        return back();

    }


    public function sendReview(Task $task, Request $request){
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
        }
        DB::commit();
        return back();
    }



}
