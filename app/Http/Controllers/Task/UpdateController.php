<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Models\Review;
use App\Models\User;
use App\Services\NotificationService;
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
            abort(403,"No Permission");

        $data = $request->validated();
        $task->addresses()->delete();
        $data['coordinates'] = $this->service->addAdditionalAddress($task,$request);
        unset($data['location0']);
        unset($data['coordinates0']);
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


    public function sendReview(Task $task, Request $request)
    {
        taskGuard($task);
// performer review to user
        try {
            if ($task->user_id == auth()->id()) {
                $task->status = $request->status ? Task::STATUS_COMPLETE : Task::STATUS_COMPLETE_WITHOUT_REVIEWS;
                $task->save();
                $user = User::find($task->performer_id);
                if ($request->good == 1) {
                    $user->review_good = $user->review_good + 1;
                } else {
                    $user->review_bad = $user->review_bad + 1;
                }
                $user->save();
                Review::create([
                    'description' => $request->comment,
                    'good_bad' => $request->good,
                    'task_id' => $task->id,
                    'reviewer_id' => $task->user_id,
                    'user_id' => $task->performer_id,
                ]);
                Notification::create([
                    'user_id' => $task->user_id,
                    'performer_id' => $task->performer_id,
                    'task_id' => $task->id,
                    'name_task' => $task->name,
                    'description' => 1,
                    'type' => Notification::SEND_REVIEW
                ]);

                NotificationService::sendNotificationRequest([$task->performer_id], [
                    'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
                ]);
            } elseif ($task->performer_id == auth()->id()) {
                Review::create([
                    'description' => $request->comment,
                    'good_bad' => $request->good,
                    'task_id' => $task->id,
                    'reviewer_id' => $task->performer_id,
                    'user_id' => $task->user_id,
                    'as_performer' => 1
                ]);
                Notification::create([
                    'user_id' => $task->performer_id,
                    'performer_id' => $task->user_id,
                    'task_id' => $task->id,
                    'name_task' => $task->name,
                    'description' => 1,
                    'type' => Notification::SEND_REVIEW
                ]);

                NotificationService::sendNotificationRequest([$task->user_id], [
                    'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
                ]);
                $task->performer_review = 1;
                $task->save();
            }
        }catch (\Exception $exception){
            DB::rollBack();
        }
        return back();
    }



}
