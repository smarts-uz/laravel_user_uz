<?php

namespace App\Services;

use App\Models\All_transaction;

class PaymentService
{
    public static function clickTransaction($amount)
    {
        All_transaction::create([
            'user_id' => auth()->id(),
            'amount' => $amount,
            'method' => All_transaction::DRIVER_CLICK,
            'state' => All_transaction::STATE_WAITING_PAY
        ]);
        $transaction_param = auth()->id();
        $return_url = config('payments.click.return_url');
        $service_id = config('payments.click.service_id');
        $merchant_id = config('payments.click.merchant_id');

        return "https://my.click.uz/services/pay?service_id=$service_id&merchant_id=$merchant_id&amount=$amount.00&transaction_param=$transaction_param&return_url=$return_url";
    }

    public static function paymeTransaction($amount)
    {
        return All_transaction::create([
            'user_id' => auth()->id(),
            'amount' => $amount,
            'method' => All_transaction::DRIVER_PAYME,
            'state' => All_transaction::STATE_WAITING_PAY
        ]);
    }
}
