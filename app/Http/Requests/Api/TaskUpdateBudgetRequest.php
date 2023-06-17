<?php

namespace App\Http\Requests\Api;

class TaskUpdateBudgetRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'amount' => 'required|numeric',
            'budget_type' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => __('Требуется заполнение!'),
            'budget_type.required' => __('Требуется заполнение!'),
            'task_id.numeric' => __('Поле должно быть числом'),
            'amount.numeric' => __('Поле должно быть числом'),
            'budget_type.numeric' => __('Поле должно быть числом'),
        ];
    }
}
