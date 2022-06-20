<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Models\All_transaction;

class RefillController extends Controller
{

    public function ref(Request $request)
    {
        $payment = $request->get("paymethod");
        $amount = $request->get("amount");
        switch ($payment) {
            case All_transaction::DRIVER_CLICK:
                $url = PaymentService::clickTransaction($amount);
                return redirect()->to($url);


            case All_transaction::DRIVER_PAYME:
                return view('paycom.send', ['transaction' => PaymentService::paymeTransaction($amount)]);


            default:
                return abort(400);
        }
    }
}

