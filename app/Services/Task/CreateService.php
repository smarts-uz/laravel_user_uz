<?php

namespace App\Services\Task;

use App\Models\Address;
use App\Models\Category;
use App\Models\CustomFieldsValue;
use App\Models\Task;
use Illuminate\Support\Arr;

class CreateService
{


    public function name($request)
    {
        $current_category = Category::findOrFail($request->category_id);
        return view("create.name", compact('current_category'));
    }

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

    public function delete($task)
    {
        $task->responses()->delete();
        $task->reviews()->delete();
        $task->custom_field_values()->delete();
        $task->addresses()->delete();
        $task->delete();
    }

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


}
