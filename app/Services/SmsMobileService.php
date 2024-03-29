<?php


namespace App\Services;

use Teamprodev\Eskiz\Sms;
use PlayMobile\SMS\SmsService;


class SmsMobileService
{
    /**
     *
     * Function  sms_packages
     * Mazkur metod ikkita sms paketdan biri orqali sms XABAR YUBORADI
     * @param $phone_number
     * @param $message
     * @return void
     */
    public static function sms_packages($phone_number, $message): void
    {
        $phone = preg_replace('/[+]+/', '', $phone_number);

        switch (env('SMS_PROVIDER')) {
            case('eskiz_sms'):
                try {
                    Sms::send($phone, $message);
                } catch (\Exception $e) {}
                break;
            case('playmobile_sms'):
                (new SmsService())->send($phone, $message);
                break;
        }
    }
}
