<?php

namespace App\Http\Requests\Api;

class FirebaseTokenRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
            'device_id' => 'required',
            'device_name' => 'required',
            'platform' => 'required'
        ];
    }
}
