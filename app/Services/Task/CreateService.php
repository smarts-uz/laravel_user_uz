<?php

namespace App\Services\Task;

use App\Http\Resources\NotificationResource;
use App\Models\Address;
use App\Models\Category;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SmsMobileService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;

class CreateService
{

    /**
     *
     * Function  attachCustomFields
     * Mazkur metod Task yaratishda  nom kiritadigan joyni ochib beradi
     * @param $request
     * @return View
     */
    public function name($request): View
    {
        $current_category = Category::query()->findOrFail($request->category_id);
        return view("create.name", compact('current_category'));
    }
    /**
     *
     * Function  syncCustomFields
     * Mazkur metod Task obyektiga unga tegishli bo'lgan custom fieldslarni o'chirib beradi
     * @param Task $task Task Object
     *
     */
    public function syncCustomFields(Task $task): void
    {
        $task->custom_field_values()->delete();
        $this->attachCustomFields($task);

    }


    /**
     *
     * Function  attachCustomFields
     * Mazkur metod Task obyektiga, unga tegishli bo'lgan custom fieldslarni qo'shib beradi
     * @param Task $task Task Object
     *
     */

    public function attachCustomFields(Task $task): void
    {
        foreach ($task->category->custom_fields as $data) {
            $value = new CustomFieldsValue();
            $value->task_id = $task->id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? (Arr::get(request()->all(), str_replace(' ', '_', $data->name)) ?? [null] ): null;
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
    }
    /**
     *
     * Function  delete
     * Mazkur metod Taskni o'chirib tashlaydi
     * @param  $task
     *
     */
    public function delete($task): void
    {
        $task->status = Task::STATUS_CANCELLED;
        $task->save();

        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'description' => $task->desciption ?? 'task description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => Notification::CANCELLED_TASK
        ]);

        NotificationService::sendNotificationRequest([$task->user_id], [
            'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
        ]);
        $locale = cacheLang($task->user_id);
        NotificationService::pushNotification($task->user, [
            'title' => __('3адание отменено', [], $locale),
            'body' => __('Ваше задание task_name №task_id было отменено', [
                'task_name' => $task->name, 'task_id' => $task->id,
            ], $locale)
        ], 'notification', new NotificationResource($notification));
    }

    /**
     *
     * Function  attachCustomFieldsByRoute
     * Mazkur metod Task obyektiga address qo'shish
     * @param $task
     * @param  $routeName
     * @param $request
     */
    public function attachCustomFieldsByRoute($task, $routeName, $request): void
    {
        foreach ($task->category->custom_fields()->where('route', $routeName)->get() as $data) {
            $value = $task->custom_field_values()->where('custom_field_id', $data->id)->first() ?? new CustomFieldsValue();
            $value->task_id = $task->id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? (Arr::get($request->all(), str_replace(' ', '_', $data->name)) ?? [null]): [];
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
    }

    /**
     *
     * Function  addAdditionalAddress
     * Mazkur metod Task obyektiga address qo'shish
     * @param $task
     * @param $requestAll
     * @return mixed
     */
    public function addAdditionalAddress($task, $requestAll): mixed
    {
        $data_inner = [];
        $dataMain = Arr::get($requestAll, 'coordinates0', '');

        for ($i = 0; $i < setting('site.max_address') ?? 10; $i++) {

            $location = Arr::get($requestAll, 'location' . $i, '');
            $coordinates = Arr::get($requestAll, 'coordinates' . $i, '');

            if ($coordinates) {
                if ($i === 0) {
                    $data_inner['default'] = 1;
                }
                $data_inner['location'] = $location;
                $data_inner['longitude'] = explode(',', $coordinates)[1];
                $data_inner['latitude'] = explode(',', $coordinates)[0];
                $data_inner['task_id'] = $task->id;
                Address::query()->create($data_inner);
            }
        }
        return $dataMain;
    }

    /**
     *
     * Function  perform_notification
     * Mazkur metod Task yaratganda notification va sms yuborish
     * @param $task $user
     * @param $user
     */
    public function perform_notification($task,$user): void
    {
        $performer_id = Session::get('performer_id_for_task');
        if ($performer_id) {
            /** @var User $performer */
            $performer = User::query()->findOrFail($performer_id);
            $locale = cacheLang($performer_id);
            $text_url = route("searchTask.task", $task->id);
            $message = __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                'task_name' => $text_url, 'task_id' => $task->id, 'task_user' => $user->name
            ], $locale);
            $phone_number=$performer->phone_number;
            SmsMobileService::sms_packages($phone_number, $message);

            /** @var Notification $notification */
            $notification = Notification::query()->create([
                'user_id' => $task->user_id,
                'performer_id' => $performer_id,
                'task_id' => $task->id,
                'name_task' => $task->name,
                'description' => '123',
                'type' => Notification::GIVE_TASK,
            ]);

            NotificationService::sendNotificationRequest([$performer_id], [
                'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
            ]);

            NotificationService::pushNotification($performer, [
                'title' => __('Предложение', [], $locale), 'body' => __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                    'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
                ], $locale)
            ], 'notification', new NotificationResource($notification));

            session()->forget('performer_id_for_task');
        }
        else {
            NotificationService::sendTaskNotification($task, $user->id);
        }
    }

}
