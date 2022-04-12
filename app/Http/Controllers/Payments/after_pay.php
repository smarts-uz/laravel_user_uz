<?php

$data = array(json_decode($transaction, true))[0];
$amount = (int)( $data['amount'] / 100 );
$user_id = $data['transactionable_id'];

App\Models\WalletBalance::walletBalanceUpdateOrCreate($user_id, $amount);
