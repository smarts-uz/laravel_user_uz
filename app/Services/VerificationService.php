<?php

namespace App\Services;

use App\Mail\VerificationEmail;
use App\Mail\VerifyEmail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificationService
{
    /**
     * telefon raqamni yoki emailni tasdiqlash
     * @param $needle
     * @param $user
     * @param null $phone_number
     * @param null $email
     * @throws Exception
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
                try {
                    Mail::to($email)->send(new VerificationEmail($data, $user, $email));
                }catch (Exception $e){
                    Log::error($e);
                }
            } else {
                try {
                    Mail::to($user->email)->send(new VerificationEmail($data, $user, $email));
                }catch (Exception $e){
                    Log::error($e);
                }
            }
        } else {
            $message = random_int(100000, 999999);
            $phone_number = (new CustomService)->correctPhoneNumber($phone_number);
            SmsMobileService::sms_packages($phone_number,config('app.name') . ' ' . __("Код подтверждения") . ' ' . $message);
        }
        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
    }

    /**
     * emailga tasdiqlash kodi yuborish
     * @param $needle
     * @param $user
     * @throws Exception
     */
    public static function send_verification_email($needle,$user): void
    {
        $message = random_int(100000, 999999);
        try {
            Mail::to($needle)->send(new VerifyEmail($message));
        }catch (Exception $e){
            Log::error($e);
        }

        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
    }


    /**
     * telefon raqamni tasdiqlash uchun kod yuborish
     * @param $task
     * @param $phone_number
     * @throws Exception
     */
    public static function send_verification_for_task_phone($task, $phone_number): void
    {
        $message = random_int(100000, 999999);
        $task->phone = (new CustomService)->correctPhoneNumber($phone_number);
        $task->verify_code = $message;
        $task->verify_expiration = Carbon::now()->addMinutes(2);
        $task->save();

        SmsMobileService::sms_packages($phone_number,config('app.name').' '. __("Код подтверждения") . ' ' . $message);
    }
}
