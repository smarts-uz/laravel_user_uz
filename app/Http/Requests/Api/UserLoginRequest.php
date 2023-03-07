<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

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
    public function authenticate(): void
    {
        $user = User::where('email',$this->email)
            ->orWhere('phone_number', $this->email)
            ->first();

        if (!$user || !Hash::check($this->password, $user->password)){
            throw new HttpResponseException(response()->json([
             'success' => false,
             'message' => __('Электронная почта или пароль неверны. Попробуй снова'),
            ]));
        }
        if (!$user->isActive()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => __('Аккаунт отключен'),
            ]));
        }
        auth()->login($user);
        if (!$user->is_email_verified  && $user->email) {
            VerificationService::send_verification('email', auth()->user());
        }
    }


    public function messages()
    {
        return [
                'email.required' => __('Требуется заполнение!'),
                'password.required' => __('Требуется заполнение!'),
            ];
    }
}
