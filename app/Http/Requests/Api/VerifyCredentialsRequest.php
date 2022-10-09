<?php

namespace App\Http\Requests\Api;

class VerifyCredentialsRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'type' => 'required|in:phone_number,email',
            'data' => $this->get('type') == 'email' ? 'required|email' : 'required'
        ];
    }
}
