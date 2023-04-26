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
}
