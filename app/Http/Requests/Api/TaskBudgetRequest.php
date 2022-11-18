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

    public function messages()
    {
        return [
            'task_id.required' => __('Требуется заполнение!'),
            'amount.required' => __('Требуется заполнение!'),
            'budget_type.required' => __('Требуется заполнение!'),
            'task_id.numeric' => __('Поле должно быть числом'),
            'amount.numeric' => __('Поле должно быть числом'),
            'budget_type.numeric' => __('Поле должно быть числом'),
        ];
    }
}
