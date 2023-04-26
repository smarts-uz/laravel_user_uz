<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class TaskUpdateContactRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'phone_number' => [
                'required',
                Rule::unique('users')->ignore(auth()->id())
            ],
        ];
    }

    public function getValidatorInstance()
    {
        $this->formatPhoneNumber();

        return parent::getValidatorInstance();
    }

    protected function formatPhoneNumber()
    {
        $phone = $this->get('phone_number');
        if (!str_starts_with($phone, '+')) {
            $this->replace([
                'phone_number' => '+' . $phone
            ]);
        }
    }

    public function messages()
    {
        return [
            "phone_number.required" => __('Требуется заполнение!'),
            "phone_number.unique" => __('Этот номер есть в системе!'),
        ];
    }
}
