<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\LoginController;
use App\Http\Requests\Api\BaseRequest;
use App\Models\User;
use Faker\Provider\Base;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class UserLoginRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required',
            'password'=> 'required'
        ];
    }
    public function authenticate(){

        $user = User::where('email',$this->email)
            ->orWhere('phone_number', $this->email)
            ->first();

         if (!$user || !Hash::check($this->password, $user->password)){
             throw new HttpResponseException(response()->json([
                 'success' => false,
                 'message' => 'Email or password incorrect',
             ]));
         }
        auth()->login($user);
        if (!$user->is_email_verified)
            LoginController::send_verification('email', auth()->user());

    }


    public function messages()
    {
        return [
                'name.required' => __('login.name.required'),
                'name.unique' => __('login.name.unique'),
                'phone_number.required' => __('login.phone_number.required'),
                'phone_number.regex' => __('login.phone_number.regex'),
                'phone_number.unique' => __('login.phone_number.unique'),
                'email.required' => __('login.email.required'),
                'email.email' => __('login.email.email'),
                'email.unique' => __('login.email.unique'),
                'password.required' => __('login.password.required'),
                'password.min' => __('login.password.min'),
                'password.confirmed' => __('login.password.confirmed'),
            ];
    }
}
