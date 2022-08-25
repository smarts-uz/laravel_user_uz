<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class TaskContactsRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'phone_number' => [
                'required',
                Rule::unique('users')->ignore(auth()->id())
            ],
            'task_id' => 'required',
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
            $this->request->replace([
                'phone_number' => '+' . $phone
            ]);
        }
    }
}
