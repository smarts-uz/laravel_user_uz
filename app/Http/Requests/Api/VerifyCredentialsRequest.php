<?php

namespace App\Http\Requests\Api;

use JetBrains\PhpStorm\ArrayShape;

class VerifyCredentialsRequest extends BaseRequest
{
    #[ArrayShape([])]
    public function rules()
    {
        return [
            'type' => 'required|in:phone_number,email',
            'data' => 'required'
        ];
    }
}
