<?php

namespace App\Http\Requests\Api;

class TaskDateRequest extends BaseRequest
{

    public function rules()
    {
        switch ($this->request->get('date_type')) {
            case 1:
                return [
                    'task_id' => 'required',
                    'start_date' => 'required|date|after:now',
                    'date_type' => 'required'
                ];
            case 2:
                return [
                    'task_id' => 'required',
                    'end_date' => 'required|date|after:now',
                    'date_type' => 'required'
                ];
            case 3:
                return [
                    'task_id' => 'required',
                    'start_date' => 'required|date|after:now',
                    'end_date' => 'required|date|after:start_date',
                    'date_type' => 'required'
                ];
        }
    }

    public function messages()
    {
        return [
            "start_date.required" => __('dateTime.start_date.required'),
            "start_date.date" => __('dateTime.start_date.date'),
            "end_date.required" => __('dateTime.end_date.required'),
            "end_date.date" => __('dateTime.end_date.date'),
            "start_date.after" => __('dateTime.start_date.after'),
            "end_date.after" => __('dateTime.end_date.after'),
        ];
    }
}
