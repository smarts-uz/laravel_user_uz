<?php

namespace App\Http\Requests\Api;

class TaskNoteRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'task_id' => 'required',
            'description' => 'required|string',
            'oplata' => 'required',
            'docs' => ''
        ];
    }
}
