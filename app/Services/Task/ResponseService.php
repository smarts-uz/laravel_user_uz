<?php


namespace App\Services\Task;


use App\Http\Resources\NotificationResource;
use App\Models\All_transaction;
use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExpense;
use App\Models\WalletBalance;
use App\Services\NotificationService;
use App\Services\SmsMobileService;
use JetBrains\PhpStorm\ArrayShape;
use App\Http\Requests\Api\TaskResponseRequest;

class ResponseService
{
    /**
     *
     * Function  store
     * Mazkur metod taskka otklik tashlaganda ishlaydi
     * @param $request
     * @param $task
     * @return array
     */
    public function store(TaskResponseRequest $request, $task): array
    {
        /** @var User $auth_user */
        $auth_user = auth()->user();
        if ($task->user_id == $auth_user->id)
            abort(403,"Bu o'zingizning taskingiz");
        $data = $request->validated();
        $data['notificate'] = $request->notificate ? 1 : 0;
        $data['task_id'] = $task->id;
        $data['user_id'] = $task->user_id;
        $data['performer_id'] = $auth_user->id;
        /** @var WalletBalance $balance */
        $balance = WalletBalance::query()->where('user_id', $auth_user->id)->first();
        if ($balance) {
            $freeResponsesCount = TaskResponse::query()->where(['performer_id' => $data['performer_id'], 'not_free' => 0])->get()->count();
            if ($request->get('not_free') == 1) {
                $balanceSufficient = $balance->balance < setting('admin.pullik_otklik') + $freeResponsesCount * setting('admin.bepul_otklik');
            } else {
                $balanceSufficient = $balance->balance < setting('admin.bepul_otklik') + $freeResponsesCount * setting('admin.bepul_otklik');
            }
            if ($balanceSufficient) {
                $success = false;
                $message = __('Недостаточно баланса');
            }else if($task->responses()->where('performer_id', $auth_user->id)->first()){
                $success = false;
                $message = __('Уже было');
            } else {
                $success = true;
                $message = __('Выполнено успешно');
                TaskResponse::query()->create($data);
                if ($request->get('not_free') == 1) {
                    $balance->balance = $balance->balance - setting('admin.pullik_otklik');
                    $balance->save();
                    UserExpense::query()->create([
                        'user_id' => $data['performer_id'],
                        'task_id' => $data['task_id'],
                        'client_id' => $data['user_id'],
                        'amount' => setting('admin.pullik_otklik')
                    ]);
                    Transaction::query()->create([
                        'payment_system' => Transaction::DRIVER_TASK,
                        'amount' => setting('admin.pullik_otklik'),
                        'system_transaction_id' => rand(10000000000, 99999999999),
                        'currency_code' => 860,
                        'state' => Transaction::STATE_COMPLETED,
                        'transactionable_type' => User::class,
                        'transactionable_id' => $data['performer_id'],
                    ]);
                }


                NotificationService::sendResponseToTaskNotification($task);
            }
        } else {
            $success = false;
            $message = __('Недостаточно баланса');
        }

        return compact('success', 'message');

    }

    /**
     *
     * Function  selectPerformer
     * Mazkur metod taskka tashlangan otkliklar orasidan ispolnitelni tanlashda ishlatiladi
     * @param $response
     * @return array
     */
    #[ArrayShape(['success' => "bool", 'message' => "mixed", 'data' => "array"])]
    public function selectPerformer($response): array
    {
        $task = $response->task;
        if ($task->status >= 3 || auth()->id() == $response->performer_id ) {
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
            $text_url = route("searchTask.task",$response->task_id);
            $message = __('Вас выбрали исполнителем  в задании task_name №task_id task_user', [
                'task_name' => $text_url, 'task_id' => $task->id, 'task_user' => $name
            ]);
            $phone_number=$performer->phone_number;
            $sms_service = new SmsMobileService();
            $sms_service->sms_packages($phone_number, $message);
        }
        $data = [
            'performer_name' => $performer->name,
            'performer_phone' => $performer->phone_number,
            'performer_description' => $performer->description,
            'performer_avatar' => asset('storage/' . $performer->avatar),
        ];
        /** @var Notification $notification */
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
        NotificationService::pushNotification($performer, [
            'title' => __('Вас выбрали исполнителем'), 'body' => __('Вас выбрали исполнителем  в задании task_name №task_id task_user', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name])
        ], 'notification', new NotificationResource($notification));
        $taskResponse = TaskResponse::query()->where(['task_id' => $task->id])->where(['performer_id' => $performer->id])->first();
        if ($taskResponse->not_free == 0) {
            /** @var WalletBalance $balance */
            $balance = WalletBalance::query()->where('user_id', $performer->id)->first();
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
        return ['success' => true,'message' => __('Выполнено успешно'), 'data' => $data];
    }




}
