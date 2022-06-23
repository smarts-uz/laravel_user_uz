<?php


namespace App\Services;

use Napa\R19\Sms;
use PlayMobile\SMS\SmsService;

class SmsTextService
{
    /**
     *
     * Function  sms_packages
     * Mazkur metod ikkita sms paketdan biri orqali sms boradi boradi
     * @param   Object
     * @return
     */
    public function sms_packages($phone_number, $text){

        $phone_numberr=preg_replace('/[+]+/', '', $phone_number);
        switch(env('SMS_PROVIDER')) {
            case('eskiz_sms'):
                Sms::send($phone_numberr, $text);
                break;
            case('playmobile_sms'):
                (new SmsService())->send($phone_number, $text);
                break;
        }

    }
}
