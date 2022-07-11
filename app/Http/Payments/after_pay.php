<?php

$data = array(json_decode($transaction, true))[0];

if ($data['payment_system'] == 'payme') {
    $amount = (int)$data['amount'];
} elseif ($data['payment_system'] == 'paynet' or $data['payment_system'] == 'Paynet') {
    $amount = (int)( $data['amount'] / 100 );
} else {
    $amount = (int)$data['amount'];
}

$user_id = $data['transactionable_id'];

App\Models\WalletBalance::walletBalanceUpdateOrCreate($user_id, $amount);

App\Services\NotificationService::sendBalanceReplenished($user_id, $amount, $data['payment_system'], $data['id']);
