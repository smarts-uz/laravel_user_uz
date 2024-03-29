<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Support\Arr;

class CustomFieldService
{
    /**
     *
     * Function  getCustomFieldsByRoute
     * @param int $task_id
     * @param string $routeName
     * @return  array
     */
    public function getCustomFieldsByRoute(int $task_id, string $routeName): array
    {
        $task = Task::with('category.custom_fields.custom_field_values')->find($task_id);
        $custom_fields = collect($task->category->custom_fields)->where('route', $routeName)->all();
        $result['task'] = $task;
        $result['category'] = $task->category;
        $result['custom_fields'] = [];
        $values = $this->getValuesOfTask($task);
        foreach ($custom_fields as $custom_field) {
            $result['custom_fields'][] = $this->initCustomField($custom_field, $task, $values);
        }
        return $result;
    }

    /**
     *
     * Function  initCustomField
     * @param $custom_field
     * @param $task
     * @param $values
     * @return  array
     */
    private function initCustomField($custom_field, $task, $values): array
    {
        $item = [];
        $item['description'] = $custom_field->getTranslatedAttribute('description', app()->getLocale());
        $item['placeholder'] = $custom_field->getTranslatedAttribute('placeholder', app()->getLocale());
        $item['title'] = $custom_field->getTranslatedAttribute('title', app()->getLocale());
        $item['name'] = $custom_field->getTranslatedAttribute('name', app()->getLocale());
        $item['label'] = $custom_field->getTranslatedAttribute('label', app()->getLocale());
        $item['type'] = $custom_field->type;
        $item['options'] = $this->setOption($custom_field, $values);
        $item['required'] = $custom_field->required;
        $item['regex'] = $custom_field->regex;
        $item['min'] = $custom_field->min;
        $item['max'] = $custom_field->max;
        $item['data_type'] = $custom_field->data_type;
        $item['order'] = $custom_field->order;
        if (!empty($values) && Arr::get($values, $custom_field->id . '.0', [])) {
            $item['task_value'] = ($custom_field->type === 'input' || $custom_field->type === 'number') ? (string)$values[$custom_field->id][0] : '';
        } else {
            $item['task_value'] = '';
        }
        return $item;
    }

    /**
     *
     * Function  setOption
     * @param $custom_field
     * @param $values
     * @return  array
     */
    private function setOption($custom_field, $values)
    {
        $options = app()->getLocale() === 'uz' ? Arr::get($custom_field->options, 'options', []) : Arr::get($custom_field->options, 'options_ru', []);
        if(empty($options) && !count($options)) {$options = Arr::get($custom_field->options, 'options', []);}
        $item = [];
        $data = [];
        foreach ($options as $key => $option) {
            $haystack = $values[$custom_field->id];
            $select = $custom_field->type === 'select' || $custom_field->type === 'radio' || $custom_field->type === 'checkbox';
            $item['id'] = $key;
            if ($haystack === null) {$item['selected'] = false;} else{
                $item['selected'] = $select ? in_array((string)$key, $haystack, true) : true;
            }
            $item['value'] = $option;
            $data[] = $item;
        }
        return $data;
    }

    /**
     *
     * Function  getValuesOfTask
     * @param $task
     * @return  array
     */
    private function getValuesOfTask($task): array
    {
        $data = [];
        foreach ($task->category->custom_fields as $custom_field) {
            $data[$custom_field->id] = json_decode(
                collect($custom_field->relationsToArray()['custom_field_values'])
                    ->where('task_id', $task->id)
                    ->value('value'));
        }
        return $data;
    }

    /**
     *
     * Function  setInputValue
     * @param $task
     * @param $data_id
     * @return  false|mixed|null
     */
    public static function setInputValue($task, $data_id)
    {
        $array = isset($task) && $task->custom_field_values()->where('custom_field_id', $data_id)->first() ?
            json_decode($task->custom_field_values()->where('custom_field_id', $data_id)->first()->value, true) : null;
        if (is_array($array) || (is_array($array) && array_key_exists('_token', $array))) {
            $array = end($array);
        }
        return $array;
    }
}
