<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Teamprodev\Laravel_Payment_Clickuz\Models\Complete;
use Teamprodev\Laravel_Payment_Clickuz\Models\ClickTransaction;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletBalance;
use TCG\Voyager\Models\User;

class ClickuzController extends Controller
{

    public function test(){
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', 'https://user.uz/get_info_click', [
            'form_params' => [
        'params' => array(
            'user_id' => 1
        )
            ]
        ])->getBody();

      // return $res;
    }

    public function pay(Request $request){

        $amount = $request->get("amount");
        $article_id = $request->get("user_id");
        $return_url = env('CLICKUZ_RETURN_URL');
        $service_id = env('CLICKUZ_SERVICE_ID');
        $merchant_id = env('CLICKUZ_MERCHANT_ID');
        return redirect()->to("https://my.click.uz/services/pay?service_id=$service_id&merchant_id=$merchant_id&amount=$amount.00&transaction_param=$article_id&return_url=$return_url");
    }

    public function get_info(Request $request){
        //Get_info parametrs
        $doc = User::where('id', $request->params['user_id'])->first();

        if($doc !== NULL){

        $wbal = WalletBalance::where('user_id', $doc->id)->first();

        if(isset($wbal)){
            $wallbal = $wbal->balance;
        }else{
            $wallbal = 0;
        }

        $res = array(
            'error' => 0,
            'error_note' => 'Успешно',
            'params' => array(
                'user_name' => $doc->name,
                'user_current_balance' => $wallbal
            )
            );
        }else{
            $res = array(
                'error' => -1,
                'error_note' => 'Абонент не найден'
                );
        }
            return json_encode($res, JSON_UNESCAPED_UNICODE);
            }

    public function prepare(Request $request){

        $new_prepare = Complete::create([
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

return ClickuzController::statusup($new_complete);

    }

    public function statusup($new_complete){

        $click_trans_id = $new_complete->click_trans_id;
        $merchant_trans_id = $new_complete->merchant_trans_id;
        $merchant_confirm_id = $new_complete->id;
        $error = $new_complete->error;
        $error_note = $new_complete->error_note;
        $amount = $new_complete->amount;

        $new_article = ClickTransaction::create([
            'user_id' => $merchant_trans_id,
            'amount'  => $amount,
            'status' => 1,
        ]);

        // A certain area for writing additional operations
        // Beginning

        $balance = WalletBalance::where('user_id', $new_article->user_id)->first();

        if(isset($balance)){
            $summa = 1*$balance->balance + 1*$new_article->amount;
            WalletBalance::where('user_id', $new_article->user_id)->update(['balance' => 1*$summa]);
        }else{
            WalletBalance::create([
                'user_id' => $new_article->user_id,
                'balance'  => 1*$new_article->amount,
            ]);
        }
        // The end

        return ['click_trans_id' => $click_trans_id,'merchant_trans_id' => $merchant_trans_id,'merchant_confirm_id' => $merchant_confirm_id,'error' => $error,'error_note' => $error_note];

    }

}
