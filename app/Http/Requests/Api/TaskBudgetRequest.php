<?php

namespace App\Http\Requests\Api;

class TaskBudgetRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'task_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'budget_type' => 'required|numeric'
        ];
    }
}
