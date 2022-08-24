<?php


namespace App\Services;

use Napa\R19\Sms;
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
    public function sms_packages($phone_number, $message){

        $phone =preg_replace('/[+]+/', '', $phone_number);
        switch(env('SMS_PROVIDER')) {
            case('eskiz_sms'):
                try {
                    Sms::send($phone, $message);
                } catch (\Exception) {

                }
                break;
            case('playmobile_sms'):
                (new SmsService())->send($phone, $message);
                break;
        }

    }
}
