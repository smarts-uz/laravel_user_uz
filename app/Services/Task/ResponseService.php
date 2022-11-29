<?php


namespace App\Services\Task;


use App\Http\Resources\NotificationResource;
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
    public function store( $request, $task): array
    {
        /** @var User $auth_user */
        $auth_user = auth()->user();
        if ((int)$task->user_id === (int)$auth_user->id)
            abort(403,"Bu o'zingizning taskingiz");
        $data = $request->validate([
            'description' => 'required|string',
            'price' => 'int|required',
            'notificate' => 'nullable',
            'not_free' => 'nullable|int'
        ],
        [
            'description.required' => __('login.name.required'),
            'price.required' => __('login.name.required'),
            'price.int' => __('login.name.int'),
            'not_free.int' => __('login.name.int'),
        ]);
        $data['notificate'] = $request->notificate ? 1 : 0;
        $data['task_id'] = $task->id;
        $data['user_id'] = $task->user_id;
        $data['performer_id'] = $auth_user->id;
        /** @var WalletBalance $balance */
        $balance = WalletBalance::query()->where('user_id', $auth_user->id)->first();
        if ($balance) {
            $freeResponsesCount = TaskResponse::query()->where(['performer_id' => $data['performer_id'], 'not_free' => 0])->get()->count();
            if ((int)$request->get('not_free') === 1) {
                $balanceSufficient = $balance->balance < setting('admin.pullik_otklik') + $freeResponsesCount * setting('admin.bepul_otklik');
            } else {
                $balanceSufficient = $balance->balance < setting('admin.bepul_otklik') + $freeResponsesCount * setting('admin.bepul_otklik');
            }
            switch (true){
                case $balanceSufficient :
                    $success = false;
                    $message = __('Недостаточно баланса');
                    break;
                case $task->responses()->where('performer_id', $auth_user->id)->first() :
                    $success = false;
                    $message = __('Уже было');
                    break;
                default :
                    $success = true;
                    $message = __('Выполнено успешно');
                    TaskResponse::query()->create($data);
                    if ((int)$request->get('not_free') === 1) {
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
                    break;
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
        if ($task->status >= 3 || auth()->id() === $response->performer_id ) {
            abort(403, 'No Permission');
        }
        $data = [
            'performer_id' => $response->performer_id,
            'status' => Task::STATUS_IN_PROGRESS
        ];
        $response_user = $response->user;
        $task->update($data);
        $performer = $response->performer;
        $locale = cacheLang($performer->id);
        if ($performer->phone_number) {
            $name = $response_user->name;
            $text_url = route("searchTask.task",$response->task_id);
            $message = __('Вы исполнитель в задании task_name. Контакты заказчика : task_user . phone_number' , [
                'task_name' => $text_url, 'phone_number' => $task->phone, 'task_user' => $name
            ], $locale);
            $phone_number=$performer->phone_number;
            SmsMobileService::sms_packages(correctPhoneNumber($phone_number), $message);
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
            'created_date' => $notification->created_at->format('d M'),
            'title' => NotificationService::titles($notification->type),
            'url' => route('show_notification', [$notification]),
            'description' => NotificationService::descriptions($notification)
        ]);
        NotificationService::pushNotification($performer, [
            'title' => NotificationService::titles($notification->type, $locale),
            'body' => NotificationService::descriptions($notification, $locale)
        ], 'notification', new NotificationResource($notification));
        $taskResponse = TaskResponse::query()->where(['task_id' => $task->id])->where(['performer_id' => $performer->id])->first();
        if ((int)$taskResponse->not_free === 0) {
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
            Transaction::query()->create([
                'payment_system' => Transaction::DRIVER_TASK,
                'amount' => setting('admin.bepul_otklik'),
                'system_transaction_id' => rand(10000000000, 99999999999),
                'currency_code' => 860,
                'state' => Transaction::STATE_COMPLETED,
                'transactionable_type' => User::class,
                'transactionable_id' => $performer->id,
            ]);
        }
        TaskResponse::query()->where(['task_id' => $task->id, 'not_free' => 0])->where('performer_id', '!=', $performer->id)->delete();
        return ['success' => true,'message' => __('Выполнено успешно'), 'data' => $data];
    }

}
