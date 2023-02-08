<?php

namespace App\Services\Task;


use App\Models\User;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginAPIService
{
    public function verifyCredentials($data)
    {
        $column = $data['type'];
        if (!User::query()
            ->where($column, $data['type'] === 'phone_number' ? correctPhoneNumber($data['data']) : $data['data'])
            ->whereNot('id', auth()->id())
            ->exists()
        ) {
            /** @var User $user */
            $user = auth()->user();
            Cache::put($user->id . 'user_' . $column , $data['type'] === 'phone_number' ? correctPhoneNumber($data['data']) : $data['data']);

            if ($data['type'] === 'phone_number') {
                VerificationService::send_verification($data['type'], $user, phone_number: $data['data']);
            } else {
                $user = Auth::user();
                VerificationService::send_verification_email($data['data'],$user);
            }

            return response()->json([
                'success' => true,
                'message' => __('Код отправлен!')
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => $data['type'] === 'email' ? __('Пользователь с такой почтой уже существует!') : __('Пользователь с таким номером уже существует!')
        ]);
    }

    public function verify_phone($user, $code)
    {
        if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
            if ($code === $user->verify_code || $code === setting('admin.CONFIRM_CODE')) {
                $user->phone_number = correctPhoneNumber(Cache::get($user->id . 'user_phone_number'));
                $user->is_phone_number_verified = 1;
                $user->save();
                Cache::forget($user->id . 'user_phone_number');
                return response()->json([
                    'success' => true,
                    'message' => __('Ваш телефон успешно подтвержден')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Неправильный код!')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Срок действия номера истек')
        ]);
    }

    public function verify_email($user, $code)
    {
        if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
            if ($code === $user->verify_code || $code === setting('admin.CONFIRM_CODE')) {
                $user->email = Cache::get($user->id . 'user_email');
                $user->is_email_verified = 1;
                $user->save();
                Cache::forget($user->id . 'user_email');
                return response()->json([
                    'success' => true,
                    'message' => __('Ваш электронная почта успешно подтвержден')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Неправильный код!')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Срок действия номера истек')
        ]);
    }
}
