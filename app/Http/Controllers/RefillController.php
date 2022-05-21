<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetInfoClickRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prepare;
use App\Models\Complete;
use App\Models\WalletBalance;
use App\Models\All_transaction;

class RefillController extends Controller
{

    public function ref(Request $request)
    {
        $payment = $request->get("paymethod");
        switch ($payment) {
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
                $tr->amount = $request->get("amount");
                $tr->method = $tr::DRIVER_PAYME;
                $tr->state = $tr::STATE_WAITING_PAY;
                $tr->save();
                return view('paycom.send', ['transaction' => $tr]);
                break;
            case 'Paynet':
                return PaynetController::pay($request);
            default:
                return false;
        }
    }

    public function getBalanceForClick(GetInfoClickRequest $request)
    {
        $data = $request->validated();
        if ($data['action'] != 0) {
            return response()->json([
                'error' => -3,
                'error_note' => "Запрашиваемое действие не найдено"
            ]);
        }

        $user = User::query()->find($data['params']['user_id']);
        if (!$user) {
            return response()->json([
                'error' => -5,
                'error_note' => "Не найден пользователь исходя из присланных данных платежа в params"
            ]);
        }
        return response()->json([
            "error" => 0,
            "error_note" => "Успешно",
            "params" => [
                'title' => "Balance",
                'balance' => $user->walletBalance->balance ?? 0
            ]
        ]);
    }
}

