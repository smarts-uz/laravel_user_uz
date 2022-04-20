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
        return ClickuzController::pay($request);
        break;
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
