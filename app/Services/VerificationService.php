<?php

namespace App\Services;

use App\Mail\VerificationEmail;
use App\Mail\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class VerificationService
{
    /**
     * @param $needle
     * @param $user
     * @param null $phone_number
     * @param null $email
     * @throws \Exception
     */
    public static function send_verification($needle, $user, $phone_number = null, $email = null): void
    {
        if ($needle === 'email') {
            $message = sha1(time());
            $data = [
                'code' => $message,
                'user' => $user->id
            ];
            if ($email) {
                Mail::to($email)->send(new VerificationEmail($data, $user, $email));
            } else {
                Mail::to($user->email)->send(new VerificationEmail($data, $user, $email));
            }
        } else {
            $message = random_int(100000, 999999);
            SmsMobileService::sms_packages( correctPhoneNumber($phone_number),"USer.Uz ". __("Код подтверждения") . ' ' . $message);
        }
        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
    }

    /**
     * @param $needle
     * @param $user
     * @throws \Exception
     */
    public static function send_verification_email($needle,$user): void
    {
        $message = random_int(100000, 999999);
        Mail::to($needle)->send(new VerifyEmail($message));

        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
    }


    /**
     * @param $task
     * @param $phone_number
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
