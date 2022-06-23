<?php


namespace App\Services\Task;


use App\Http\Resources\NotificationResource;
use App\Models\All_transaction;
use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\UserExpense;
use App\Models\WalletBalance;
use App\Services\NotificationService;
use App\Services\SmsTextService;

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
        $balance = WalletBalance::where('user_id', auth()->user()->id)->first();
        if ($balance) {
            $freeResponsesCount = TaskResponse::query()->where(['performer_id' => $data['performer_id'], 'not_free' => 0])->get()->count();
            if ($request->get('not_free') == 1) {
                $balanceSufficient = $balance->balance < setting('admin.pullik_otklik') + $freeResponsesCount * setting('admin.bepul_otklik');
            } else {
                $balanceSufficient = $balance->balance < setting('admin.bepul_otklik') + $freeResponsesCount * setting('admin.bepul_otklik');
            }
            if ($balanceSufficient) {
                $success = false;
                $message = __('not_enough_balance');
            }else if($task->responses()->where('performer_id', auth()->user()->id)->first()){
                $success = false;
                $message = __('already_had');
            } else {
                $success = true;
                $message = __('success');
                TaskResponse::create($data);
                if ($request->get('not_free') == 1) {
                    $balance->balance = $balance->balance - setting('admin.pullik_otklik');
                    $balance->save();
                    UserExpense::query()->create([
                        'user_id' => $data['performer_id'],
                        'task_id' => $data['task_id'],
                        'client_id' => $data['user_id'],
                        'amount' => setting('admin.pullik_otklik')
                    ]);
                    All_transaction::query()->create([
                        'user_id' => $data['performer_id'],
                        'method' => All_transaction::METHODS['Task'],
                        'amount' => setting('admin.pullik_otklik'),
                        'status' => All_transaction::STATUS_SUCCESS,
                        'state' => All_transaction::STATE_PAY_ACCEPTED
                    ]);
                }


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
        if ($task->status >= 3 || auth()->user()->id == $response->performer_id ) {
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
            $phone_number=$performer->phone_number;
            $sms_service = new SmsTextService();
            $sms_service->sms_packages($phone_number, $text);
        }
        $data = [
            'performer_name' => $performer->name,
            'performer_phone' => $performer->phone_number,
            'performer_description' => $performer->description,
            'performer_avatar' => asset('storage/' . $performer->avatar),
        ];
        $notification = Notification::query()->create([
            'user_id' => $response_user->id,
            'performer_id' => $performer->id,
            'task_id' => $response->task_id,
            'name_task' => $task->name,
            'description' => '123',
            'type' => Notification::SELECT_PERFORMER,
        ]);
        NotificationService::sendNotificationRequest([$performer->id], [
            'url' => 'detailed-tasks' . '/' . $response->task_id, 'name' => $task->name, 'time' => 'recently'
        ]);
        NotificationService::pushNotification($performer->firebase_token, [
            'title' => 'You are selected for task', 'body' => 'See details'
        ], 'notification', new NotificationResource($notification));
        $taskResponse = TaskResponse::query()->where(['task_id' => $task->id])->where(['performer_id' => $performer->id])->first();
        if ($taskResponse->not_free == 0) {
            $balance = WalletBalance::where('user_id', $performer->id)->first();
            $balance->balance = $balance->balance - setting('admin.bepul_otklik');
            $balance->save();
            UserExpense::query()->create([
                'user_id' => $performer->id,
                'task_id' => $task->id,
                'client_id' => $response_user->id,
                'amount' => setting('admin.bepul_otklik')
            ]);
            All_transaction::query()->create([
                'user_id' => $performer->id,
                'method' => All_transaction::METHODS['Task'],
                'amount' => setting('admin.bepul_otklik'),
                'status' => 0,
                'state' => 1
            ]);
        }
        TaskResponse::query()->where(['task_id' => $task->id, 'not_free' => 0])->where('performer_id', '!=', $performer->id)->delete();
        return ['success' => true,'message' => __('success'), 'data' => $data];
    }




}
