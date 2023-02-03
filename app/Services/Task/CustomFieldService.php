<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Support\Arr;

class CustomFieldService
{
    /*public function getCustomFieldsByRoute($task, $routeName): array
    {
        $custom_fields = $task->category->custom_fields()->where('route', $routeName)->get();
        $result = [];
        $values = $this->getValuesOfTask($task);

        foreach ($custom_fields as $custom_field) {
            $result[] = $this->initCustomField($custom_field, $task, $values);
        }
        return $result;
    }*/

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

    public function getCustomFields($task): array
    {
        $custom_fields = $task->category->custom_fields;
        $result = [];
        $values = $this->getValuesOfTask($task);
        foreach ($custom_fields as $custom_field) {
            $result[] = $this->initCustomField($custom_field, $task, $values);
        }
        return $result;
    }

    private function initCustomField($custom_field, $task, $values): array
    {
        $item = [];
        $item['description'] = $custom_field->getTranslatedAttribute('description', app()->getLocale());
        $item['placeholder'] = $custom_field->getTranslatedAttribute('placeholder', app()->getLocale());
        $item['title'] = $custom_field->getTranslatedAttribute('title', app()->getLocale());
        $item['label'] = $custom_field->getTranslatedAttribute('label', app()->getLocale());
        $item['type'] = $custom_field->type;
        $item['options'] = $this->setOption($custom_field, $task);
        $item['values'] = $custom_field->values;
        $item['required'] = $custom_field->required;
        $item['regex'] = $custom_field->regex;
        $item['min'] = $custom_field->min;
        $item['max'] = $custom_field->max;
        $item['data_type'] = $custom_field->data_type;
        $item['order'] = $custom_field->order;
        $item['name'] = $custom_field->name;
        if (count($values[$custom_field->id])) {
            $item['task_value'] = ($custom_field->type === 'input' or $custom_field->type === 'number') ? (string)Arr::get($values,$custom_field->id . '0') : '';
        } else {
            $item['task_value'] = ($custom_field->type === 'input' or $custom_field->type === 'number') ? '' : '';
        }
        return $item;
    }

    private function setOption($custom_field, $task)
    {
        $values = $this->getValuesOfTask($task);

        $options = app()->getLocale() === 'uz' ? Arr::get($custom_field->options, 'options', []) : Arr::get($custom_field->options, 'options_ru', []);
        $options = empty($options) ? Arr::get($custom_field->options, 'options', []) : Arr::get($custom_field->options, 'options_ru', []);
        $item = [];
        $data = [];
        foreach ($options as $key => $option) {
            $haystack = $values[$custom_field->id];
            $item['id'] = $key;
            $item['selected'] = in_array((string)$key, $haystack, true);
            $item['value'] = $option;
            $data[] = $item;
        }
        return $data;
    }

    private function getValuesOfTask($task): array
    {
        $data = [];
        foreach ($task->category->custom_fields as $custom_field) {
            $data[$custom_field->id] = [];
        }
        foreach ($task->custom_field_values as $custom_fields_value) {
            $data[$custom_fields_value->custom_field_id] = $custom_fields_value->value ? json_decode($custom_fields_value->value) : [];
        }
        return $data;
    }


    public static function showOptions($task, $data_id, $key, $option): bool
    {
        if (isset($task)) {
            $field = $task->custom_field_values()->where('custom_field_id', $data_id)->first();
            if ($field && is_array(json_decode($field->value)) && in_array($option, json_decode($field->value))) {
                return true;
            }
        }
        return false;
    }

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
