<?php

namespace App\Services;

use Carbon\Carbon;

class VerificationService
{
    /**
     * @throws \Exception
     */
    public static function send_verification($needle, $user, $phone_number = null, $email = null): void
    {
        $message = random_int(100000, 999999);
        SmsMobileService::sms_packages( correctPhoneNumber($phone_number),config('app.name').' '. __("Код подтверждения") . ' ' . $message);
        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();

    }

    /**
     * @throws \Exception
     */
    public static function send_verification_for_task_phone($task, $phone_number): void
    {
        $message = random_int(100000, 999999);
        $task->phone = $phone_number;
        $task->verify_code = $message;
        $task->verify_expiration = Carbon::now()->addMinutes(2);
        $task->save();

        SmsMobileService::sms_packages(correctPhoneNumber($phone_number),config('app.name').' '. __("Код подтверждения") . ' ' . $message);
    }
}
