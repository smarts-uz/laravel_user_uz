<?php

namespace App\Services;

use Carbon\Carbon;

class VerificationService
{
    public static function send_verification($needle, $user, $phone_number, $email = null): void
    {
        if(!($user->verify_code)){
            $message = rand(100000, 999999);
        }else{
            $message = $user->verify_code;
        }
        SmsMobileService::sms_packages( correctPhoneNumber($phone_number),"USer.Uz ". __("Код подтверждения") . ' ' . $message);
        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();

    }

    public static function send_verification_for_task_phone($task, $phone_number): void
    {
        if(!($task->verify_code)){
            $message = rand(100000, 999999);
        }else{
            $message = $task->verify_code;
        }
        $task->phone = $phone_number;
        $task->verify_code = $message;
        $task->verify_expiration = Carbon::now()->addMinutes(2);
        $task->save();

        SmsMobileService::sms_packages(correctPhoneNumber($phone_number),"USer.Uz ". __("Код подтверждения") . ' ' . $message);
    }
}
