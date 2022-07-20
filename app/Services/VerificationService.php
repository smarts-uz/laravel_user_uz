<?php

namespace App\Services;

use App\Mail\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class VerificationService
{
    public static function send_verification($needle, $user, $phone_number = null, $email = null)
    {
        if ($needle == 'email') {
            $message = sha1(time());
            $data = [
                'code' => $message,
                'user' => $user->id
            ];
            Mail::to($user->email)->send(new VerifyEmail($data));
        } else {
            $message = rand(100000, 999999);
            $sms_service = new SmsMobileService();
            $sms_service->sms_packages($phone_number, $message);
        }
        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();

    }
}
