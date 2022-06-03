<?php

namespace App\Services\Task;

class CustomFieldService
{

    public function getCustomFieldsByRoute($task, $routeName)
    {
        $custom_fields = $task->category->custom_fields()->where('route', $routeName)->get();
        $result = [];
        $values = $this->getValuesOfTask($task);

        foreach ($custom_fields as $custom_field) {
            $result[] = $this->initCustomField($custom_field, $task,$values);
        }
        return $result;
    }

    public function getCustomFields($task)
    {
        $custom_fields = $task->category->custom_fields;
        $result = [];
        $values = $this->getValuesOfTask($task);
        foreach ($custom_fields as $custom_field) {
            $result[] = $this->initCustomField($custom_field, $task,$values);
        }
        return $result;
    }
    private function initCustomField($custom_field,$task, $values)
    {
        $item = [];
        $item['description'] = $custom_field->getTranslatedAttribute('description');
        $item['placeholder'] = $custom_field->getTranslatedAttribute('placeholder');
        $item['title'] = $custom_field->getTranslatedAttribute('title');
        $item['label'] = $custom_field->getTranslatedAttribute('label');
        $item['type'] = $custom_field->type;
        $item['options'] = $this->setOption($custom_field,$task);
        $item['values'] = $custom_field->values;
        $item['order'] = $custom_field->order;
        $item['name'] = $custom_field->name;
        $item['task_value'] = $custom_field->type == 'input' ? count($values[$custom_field->id]) ? (string)$values[$custom_field->id][0]:'':'';
        return $item;

    }

    private function setOption($custom_field,$task)
    {
        $values = $this->getValuesOfTask($task);

        $options = app()->getLocale()=='ru' && $custom_field->options_ru ? $custom_field->options_ru: $custom_field->options;
        $options = $options ? $options['options']:[];
        $item = [];
        $data = [];
        foreach ($options as $option) {
            $item['selected'] = in_array($option, $values[$custom_field->id]);
            $item['value'] = $option;
            $data[] = $item;
        }
        return $data;
    }
    private function getValuesOfTask($task)
    {
        $data = [];
        foreach ($task->category->custom_fields as $custom_field) {
            $data[$custom_field->id] = [];
        }
        foreach ($task->custom_field_values as $custom_fields_value) {
            $data[$custom_fields_value->custom_field_id] = $custom_fields_value->value?json_decode($custom_fields_value->value):[];
        }

        return $data;
    }


    public static function showOptions($task, $data_id, $key, $option)
    {
        if (isset($task)) {
            $field = $task->custom_field_values()->where('custom_field_id', $data_id)->first();
            if (
                $field && is_array( json_decode($field->value)) &&
                array_key_exists($key-1, json_decode($field->value)) &&
                json_decode($field->value, true)[$key-1] == $option
            ) {
                return true;
            }
        }
        return false;
    }

    public static function setInputValue($task, $data_id)
    {
        $array = isset($task) && $task->custom_field_values()->where('custom_field_id', $data_id)->first() ?
            json_decode($task->custom_field_values()->where('custom_field_id', $data_id)->first()->value, true) : null;
        if (is_array($array) || is_array($array) && array_key_exists('_token', $array)) {
            $array = end($array);
        }

        return $array;
    }
}
