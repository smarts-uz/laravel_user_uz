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

    if($request->get("paymethod") == 'Click'){

        $new_article = All_transaction::create([

            'user_id'=> Auth::id(),
            'amount'=> $request->get("amount"),
            'method'=> "Click",

        ]);

        $amount = $request->get("amount");
        $article_id = $new_article->id;

      return redirect()->to("https://my.click.uz/services/pay?service_id=19839&merchant_id=14364&amount=$amount.00&transaction_param=$article_id&return_url=https://youdo.teampro.uz");

      }

    if($request->get("paymethod") == 'PayMe'){

        //Integration with PayMe

    }

}

public function prepare(Request $request){

    $new_prepare = Prepare::create([

        'click_trans_id'=> $request->get("click_trans_id"),
        'service_id'=> $request->get("service_id"),
        'click_paydoc_id'=> $request->get("click_paydoc_id"),
        'merchant_trans_id'=> $request->get("merchant_trans_id"),
        'amount'=> $request->get("amount"),
        'action'=> $request->get("action"),
        'error'=> $request->get("error"),
        'error_note'=> $request->get("error_note"),
        'sign_time'=> $request->get("sign_time"),
        'sign_string'=> $request->get("sign_string"),


    ]);


    $click_trans_id = $new_prepare->click_trans_id;
    $merchant_trans_id = $new_prepare->merchant_trans_id;
    $merchant_prepare_id = $new_prepare->id;
    $error = $new_prepare->error;
    $error_note = $new_prepare->error_note;

    return ['click_trans_id' => $click_trans_id,'merchant_trans_id' => $merchant_trans_id,'merchant_prepare_id' => $merchant_prepare_id,'error' => $error,'error_note' => $error_note];

}


public function complete(Request $request){

    $new_complete = Complete::create([

        'click_trans_id'=> $request->get("click_trans_id"),
        'service_id'=> $request->get("service_id"),
        'click_paydoc_id'=> $request->get("click_paydoc_id"),
        'merchant_trans_id'=> $request->get("merchant_trans_id"),
        'merchant_prepare_id'=> $request->get("merchant_prepare_id"),
        'amount'=> $request->get("amount"),
        'action'=> $request->get("action"),
        'error'=> $request->get("error"),
        'error_note'=> $request->get("error_note"),
        'sign_time'=> $request->get("sign_time"),
        'sign_string'=> $request->get("sign_string"),


    ]);

    $click_trans_id = $new_complete->click_trans_id;
    $merchant_trans_id = $new_complete->merchant_trans_id;
    $merchant_confirm_id = $new_complete->id;
    $error = $new_complete->error;
    $error_note = $new_complete->error_note;

    $user = All_transaction::where('id', $merchant_trans_id)->first();

    $balance = WalletBalance::where('user_id', $user->user_id)->first();

    if(isset($balance)){
    $summa = $balance->balance + $user->amount;
    }else{
        WalletBalance::create([
            'user_id'=> $user->id,
            'amount'=> $user->amount,
        ]);
        $summa = $user->amount;
    }

    WalletBalance::where('user_id', $user->user_id)->update(['balance' => $summa]);
    All_transaction::where('id', $user->id)->update(['status' => 1]);

    return ['click_trans_id' => $click_trans_id,'merchant_trans_id' => $merchant_trans_id,'merchant_confirm_id' => $merchant_confirm_id,'error' => $error,'error_note' => $error_note];

}

}
