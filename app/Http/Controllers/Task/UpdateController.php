<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\Notification;
use App\Models\Task;
use App\Models\Review;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Services\Task\CreateService;
use Illuminate\Support\Facades\DB;
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
            abort(403, "No Permission");

        $data = $request->validated();
        $task->addresses()->delete();
        $data['coordinates'] = $this->service->addAdditionalAddress($task, $request);
        unset($data['location0']);
        unset($data['coordinates0']);
        $task->update($data);
        $note = $request->validate([
            'description' => 'required|string',
            'oplata' => 'required',
        ]);
        if ($request['docs'] === "on") {
            $note['docs'] = 1;
        } else {
            $note['docs'] = 0;
        }
        $task->update($note);
        $this->service->syncCustomFields($task);
        Alert::success('Success');

        return redirect()->route('searchTask.task', $task->id);


    }

    public function completed(Task $task)
    {
        $data = [
            'status' => Task::STATUS_COMPLETE
        ];
        $task->update($data);

        Alert::success('Success');

        return back();

    }

    public function not_completed(Task $task)
    {
        $task->update(['status' => Task::STATUS_COMPLETE_WITHOUT_REVIEWS]);
        Alert::success('Success');
        return back();
    }

    public function sendReview(Task $task, Request $request)
    {
        taskGuard($task);
// performer review to user
        try {
            if ($task->user_id == auth()->id()) {
                $task->status = Task::STATUS_COMPLETE;
                $task->save();
                $performer = User::find($task->performer_id);
                if ($request->good == 1) {
                    $performer->increment('review_good');
                } else {
                    $performer->increment('review_bad');
                }
                $performer->increment('reviews');
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
                Review::query()->create([
                    'description' => $request->comment,
                    'good_bad' => $request->good,
                    'task_id' => $task->id,
                    'reviewer_id' => $task->performer_id,
                    'user_id' => $task->user_id,
                    'as_performer' => 1
                ]);
                Notification::create([
                    'user_id' => $task->user_id,
                    'performer_id' => $task->performer_id,
                    'task_id' => $task->id,
                    'name_task' => $task->name,
                    'description' => 1,
                    'type' => Notification::SEND_REVIEW_PERFORMER
                ]);
                NotificationService::sendNotificationRequest([$task->user_id], [
                    'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
                ]);
                $task->performer_review = 1;
                $task->save();
            }
        } catch (\Exception $exception) {
            DB::rollBack();
        }
        return back();
    }


}
