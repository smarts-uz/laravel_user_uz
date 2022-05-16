<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prepare;
use App\Models\Complete;
use App\Models\WalletBalance;
use App\Models\All_transaction;

class RefillController extends Controller
{

public function ref(Request $request){

    $payment = $request->get("paymethod");
    switch($payment){
        case 'Click':
            $amount = $request->get("amount");
            $article_id = $request->get("user_id");
            $return_url = config('click.return_url');
            $service_id = config('click.service_id');
            $merchant_id = config('click.merchant_id');
            return redirect()->to("https://my.click.uz/services/pay?service_id=$service_id&merchant_id=$merchant_id&amount=$amount.00&transaction_param=$article_id&return_url=$return_url");
        case 'PayMe':
            $tr = new All_transaction();
            $tr->user_id = Auth::id();
            $tr->amount  = $request->get("amount");
            $tr->method  = $tr::DRIVER_PAYME;
            $tr->state   = $tr::STATE_WAITING_PAY;
            $tr->save();
            return view('paycom.send', ['transaction' => $tr]);
        break;
        case 'Paynet':
            return PaynetController::pay($request);
        break;
    }

}

}
