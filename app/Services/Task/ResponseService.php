<?php


namespace App\Services\Task;


use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\WalletBalance;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use PlayMobile\SMS\SmsService;
use RealRashid\SweetAlert\Facades\Alert;

class ResponseService
{

    public function store($request, $task)
    {
        if ($task->user_id == auth()->user()->id)
            abort(403,"Bu o'zingizning taskingiz");
        $data = $request->validate([
            'description' => 'required|string',
            'price' => 'required|int',
            'notificate' => 'nullable',
            'not_free' => 'nullable|int'
        ]);
        $data['notificate'] = $request->notificate ? 1 : 0;
        $data['task_id'] = $task->id;
        $data['user_id'] = $task->user_id;
        $data['performer_id'] = auth()->user()->id;
        $ballance = WalletBalance::where('user_id', auth()->user()->id)->first();
        if ($ballance) {
            if ($ballance->balance < setting('admin.pullik_otklik')) {
                $success = false;
                $message = __('not_enough_balance');
            }else if($task->responses()->where('performer_id', auth()->user()->id)->first()){
                $success = false;
                $message = __('already_had');
            } else {
                $success = true;
                $message = __('success');
                $ballance->balance = $ballance->balance - $request->pay;
                $ballance->save();
                $r = TaskResponse::create($data);

                NotificationService::sendTaskSelectedNotification($task);
            }
        } else {
            $success = false;
            $message = __('not_enough_balance');
        }

        return compact('success', 'message');

    }


    public function selectPerformer($response)
    {
        $task = $response->task;
        if ($task->status >= 3 || $task->resonses_count ||  auth()->user()->id == $response->performer_id ) {
            abort(403, 'No Permission');
        }
        $data = [
            'performer_id' => $response->performer_id,
            'status' => Task::STATUS_IN_PROGRESS
        ];
        $response_user = $response->user;
        $task->update($data);
        $performer = $response->performer;
        if ($performer->phone_number) {
            $name = $response_user->name;
            $phone = $response_user->phone_number;
            $tesk_url = route("searchTask.task",$response->task_id);
            $text = "Vi ispolnitel v zadanii $tesk_url. Kontakt zakazchika: $name. $phone";
            (new SmsService())->send($performer->phone_number, $text);
        }
        $data = [
            'performer_name' => $performer->name,
            'performer_phone' => $performer->phone_number,
            'performer_description' => $performer->description,
            'performer_avatar' => asset('storage/' . $performer->avatar),
        ];

        Notification::query()->create([
            'user_id' => $response_user->id,
            'performer_id' => $performer->id,
            'task_id' => $response->task_id,
            'name_task' => $task->name,
            'description' => '123',
            'type' => 7,
        ]);
        NotificationService::sendNotificationRequest([$performer->id], [
            'url' => 'detailed-tasks' . '/' . $response->task_id, 'name' => $task->name, 'time' => 'recently'
        ]);
        return ['success' => true,'message' => __('success'), 'data' => $data];
    }




}
