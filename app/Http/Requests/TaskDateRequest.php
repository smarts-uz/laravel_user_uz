<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskDateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->request->get('date_type')) {
            case 1:
                return [
                    'start_date' => 'required|date|after:now',
                    'date_type' => 'required'
                ];
            case 2:
                return [
                    'end_date' => 'required|date|after:now',
                    'date_type' => 'required'
                ];
            case 3:
                return [
                    'start_date' => 'required|date|after:now',
                    'end_date' => 'required|date|after:start_date',
                    'date_type' => 'required'
                ];
        }
    }

    public function messages()
    {
        return [
            "start_date.required" => __('Вы выбрали прошедшую дату'),
            "start_date.date" => __('dateTime.start_date.date'),
            "end_date.required" => __('dateTime.end_date.required'),
            "end_date.date" => __('dateTime.end_date.date'),
            "start_date.after" => __('Время начала должно быть позже времени создания'),
            "end_date.after" => __('Время окончания должно быть позже времени начала'),
        ];
    }
}
