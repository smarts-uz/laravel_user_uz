<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use RealRashid\SweetAlert\Facades\Alert;

class UserTransactionHistory extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getTransactions (): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $payment = strtolower($_GET['method']);
        if(in_array($payment, Transaction::METHODS) ||  $payment === 'task') {
            $transactionMethod = Transaction::query()
                ->where('payment_system', strtolower($_GET['method']))
                ->where(['transactionable_id' => $user->id]);
        } else {
            Alert::error(__('Неопределенный способ оплаты'));
            return response()->json([
                'success' => false,
                'message' => 'Undefined payment method'
            ]);
        }

        if (array_key_exists('period', $_GET)){
            $filter = match ($_GET['period']) {
                'month' => now()->subMonth(),
                'week' => now()->subWeek(),
                'year' => now()->subYear(),
                default => now(),
            };
            $transactions = $transactionMethod->where('created_at', '>', $filter)->get();
        } else {
            $from = $_GET['from_date'];
            $to = $_GET['to_date'];
            $transactions = $transactionMethod->where('created_at', '>', $from)
                ->where('created_at', '<', $to)->get();
        }
        $data = [];
        foreach ($transactions as $transaction) {
            $amount = ucfirst($transaction->payment_system) === 'Paynet' ? $transaction->amount / 100 : $transaction->amount;
            $created_at = $transaction->created_at;
            $date = new Carbon($created_at);
            $data[] = ['amount' => $amount, 'created_at' => $date->toDateTimeString()];
        }
        return response()->json([
            'transactions' => $data,
        ]);
    }
}
