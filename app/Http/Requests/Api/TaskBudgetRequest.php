<?php

namespace App\Http\Requests\Api;

class TaskBudgetRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'task_id' => 'required',
            'amount1' => 'required'
        ];
    }
}
