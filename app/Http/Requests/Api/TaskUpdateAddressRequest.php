<?php

namespace App\Http\Requests\Api;


class TaskUpdateAddressRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'points' => 'required|array|max:10',
            'points.*.location' => 'required|string',
            'points.*.latitude' => 'required|numeric',
            'points.*.longitude' => 'required|numeric'
        ];
    }
    public function messages()
    {
        return [
            'points.required' => __('Требуется заполнение!'),
            'points.*.location.required' => __('Требуется заполнение!'),
            'points.*.latitude.required' => __('Требуется заполнение!'),
            'points.*.longitude.required' => __('Требуется заполнение!'),
            'points.*.location.string' => __('Проверяемое поле должно быть строкой.'),
            'points.*.latitude.numeric' => __('Значение поля должно быть числом'),
            'points.*.longitude.numeric' => __('Значение поля должно быть числом'),
            'points.array' => __('Значение поля должно быть массивом'),
            'points.max' => __('Проверяемое поле должно быть меньше или равно максимальному значению'),
        ];

    }
}
