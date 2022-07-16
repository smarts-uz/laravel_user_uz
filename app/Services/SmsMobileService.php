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
     * @param   Object
     * @return
     */
    public function sms_packages($phone_number, $message){

        $phone_numberr=preg_replace('/[+]+/', '', $phone_number);
        switch(env('SMS_PROVIDER')) {
            case('eskiz_sms'):
                try {
                    Sms::send($phone_numberr, $message);
                } catch (\Exception) {

                }
                break;
            case('playmobile_sms'):
                (new SmsService())->send($phone_number, $message);
                break;
        }

    }
}
