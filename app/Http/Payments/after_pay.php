<?php

$data = array(json_decode($transaction, true))[0];

switch ($data['payment_system']){
    case 'Paynet':
    case 'paynet':
        $amount = (int)( $data['amount'] / 100 );
        break;

    default:
        $amount = (int)$data['amount'];
        break;
}

$user_id = $data['transactionable_id'];

App\Models\WalletBalance::walletBalanceUpdateOrCreate($user_id, $amount);

App\Services\NotificationService::sendBalanceReplenished($user_id, $amount, $data['payment_system'], $data['id']);
