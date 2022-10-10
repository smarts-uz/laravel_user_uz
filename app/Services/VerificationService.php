<?php

namespace App\Services;

use App\Mail\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class VerificationService
{
    public static function send_verification($needle, $user, $phone_number = null, $email = null): void
    {
        if ($needle === 'email') {
            $message = sha1(time());
            $data = [
                'code' => $message,
                'user' => $user->id
            ];
            if ($email) {
                Mail::to($email)->send(new VerifyEmail($data, $user, $email));
            } else {
                Mail::to($user->email)->send(new VerifyEmail($data, $user, $email));
            }
        } else {
            $message = rand(100000, 999999);
            SmsMobileService::sms_packages($phone_number,"USer.Uz ". __("Код подтверждения") . ' ' . $message);
        }
        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();

    }

    public static function send_verification_for_task_phone($task, $phone_number): void
    {
        $message = rand(100000, 999999);

        $task->phone = $phone_number;
        $task->verify_code = $message;
        $task->verify_expiration = Carbon::now()->addMinutes(2);
        $task->save();

        SmsMobileService::sms_packages($phone_number,"USer.Uz ". __("Код подтверждения") . ' ' . $message);
    }
}
