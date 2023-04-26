<?php

namespace App\Http\Requests\Api;

class TaskNoteRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'task_id' => 'required',
            'description' => 'required|string',
            'docs' => ''
        ];
    }

    public function messages()
    {
        return [
            'description.required' => 'Требуется заполнение!',
            'task_id.required' => 'Требуется заполнение!',
        ];
    }
}
