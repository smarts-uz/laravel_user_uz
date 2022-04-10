<?php

namespace App\Http\Controllers;

use App\Models\PaynetTransaction;
use Illuminate\Http\Request;

class PaynetController extends Controller
{
    public static function pay(Request $request)
    {
        return PaynetTransaction::create([
            'user_id' => $request->get("user_id"),
            'amount'  => $request->get("amount"),
        ]);
    }
}
