<?php

namespace App\Http\Requests\Api;

class TaskUpdateNoteRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'description' => 'required|string',
            'docs' => ''
        ];
    }

    public function messages()
    {
        return [
            'description.required' => 'Требуется заполнение!',
        ];
    }
}
