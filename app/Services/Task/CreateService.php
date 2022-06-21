<?php

namespace App\Services\Task;

use App\Models\Address;
use App\Models\Category;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SmsTextService;
use Illuminate\Support\Arr;
use PlayMobile\SMS\SmsService;

class CreateService
{

    /**
     *
     * Function  attachCustomFields
     * Mazkur metod Task yaratishda  nom kiritadigan joyni ochib beradi
     * @param Request $request Task Object
     *
     */
    public function name($request)
    {
        $current_category = Category::findOrFail($request->category_id);
        return view("create.name", compact('current_category'));
    }
    /**
     *
     * Function  syncCustomFields
     * Mazkur metod Task obyektiga unga tegishli bo'lgan custom fieldslarni o'chirib beradi
     * @param Task $task Task Object
     *
     */
    public function syncCustomFields(Task $task)
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

    public function attachCustomFields(Task $task)
    {
        foreach ($task->category->custom_fields as $data) {
            $value = new CustomFieldsValue();
            $value->task_id = $task->id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? Arr::get(request()->all(), str_replace(' ', '_', $data->name)) : null;
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
    }
    /**
     *
     * Function  delete
     * Mazkur metod Taskni o'chirib tashlaydi
     * @param  $task Object
     *
     */
    public function delete($task)
    {
        $task->responses()->delete();
        $task->reviews()->delete();
        $task->custom_field_values()->delete();
        $task->addresses()->delete();
        $task->delete();
    }
    /**
     *
     * Function  attachCustomFieldsByRoute
     * Mazkur metod Task obyektiga address qo'shish
     * @param  $routeName Object
     *
     */
    public function attachCustomFieldsByRoute($task, $routeName, $request)
    {
        foreach ($task->category->custom_fields()->where('route', $routeName)->get() as $data) {
            $value = $task->custom_field_values()->where('custom_field_id', $data->id)->first() ?? new CustomFieldsValue();
            $value->task_id = $task->id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? Arr::get($request->all(), str_replace(' ', '_', $data->name)) : null;
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
    }

    /**
     *
     * Function  addAdditionalAddress
     * Mazkur metod Task obyektiga address qo'shish
     * @param $requestAll Task Object
     *
     */
    public function addAdditionalAddress($task, $requestAll)
    {
        $data_inner = [];
        $dataMain = Arr::get($requestAll, 'coordinates0', '');

        for ($i = 0; $i < setting('site.max_address') ?? 10; $i++) {

            $location = Arr::get($requestAll, 'location' . $i, '');
            $coordinates = Arr::get($requestAll, 'coordinates' . $i, '');

            if ($coordinates) {
                if ($i == 0) {
                    $data_inner['default'] = 1;
                }
                $data_inner['location'] = $location;
                $data_inner['longitude'] = explode(',', $coordinates)[1];
                $data_inner['latitude'] = explode(',', $coordinates)[0];
                $data_inner['task_id'] = $task->id;
                Address::create($data_inner);
            }
        }
        return $dataMain;
    }


    public function perform_notif($task,$user){
        $performer_id = session()->get('performer_id_for_task');
        if ($performer_id) {
            $performer = User::query()->find($performer_id);
            $text_url = route("searchTask.task", $task->id);
            $text = "Заказчик предложил вам новую задания $text_url. Имя заказчика: " . $user->name;
            $phone_number=$performer->phone_number;;
            $sms_service = new SmsTextService();
            $sms_service->sms_packages($phone_number, $text);
            Notification::query()->create([
                'user_id' => $task->user_id,
                'performer_id' => $performer_id,
                'task_id' => $task->id,
                'name_task' => $task->name,
                'description' => '123',
                'type' => 4,
            ]);

            NotificationService::sendNotificationRequest([$performer_id], [
                'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
            ]);

            session()->forget('performer_id_for_task');
        }
        else {
            NotificationService::sendTaskNotification($task, $user->id);
        }
    }

}
