<?php

namespace App\Services;

use App\Models\All_transaction;

class PaymentService
{
    /**
     * Save click transaction to database and return click pay page url with params
     *
     * @param $amount
     * @return string
     */
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

    /**
     * Save payme transaction to database and return transaction object
     *
     * @param $amount
     * @return mixed
     */
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
