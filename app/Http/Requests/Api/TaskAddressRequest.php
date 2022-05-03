<?php

namespace App\Http\Requests\Api;


class TaskAddressRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'task_id' => 'required',
            'points' => 'required|array|max:10',
            'points.*.location' => 'required|string',
            'points.*.latitude' => 'required|numeric',
            'points.*.longitude' => 'required|numeric'
        ];
    }
}
