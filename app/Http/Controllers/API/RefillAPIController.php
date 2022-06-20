<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\All_transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class RefillAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ref",
     *     tags={"Refill"},
     *     summary="Get list of Refill",
     *     security={
     *         {"token": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Ajax error"
     *     )
     * )
     */
    public function ref(Request $request){

        $payment = $request->get("paymethod");
        $amount = $request->get("amount");
        switch($payment){
            case All_transaction::DRIVER_CLICK:
                $url = PaymentService::clickTransaction($amount);
                return redirect()->to($url);


            case All_transaction::DRIVER_PAYME:
                return response()->json(['transaction' => PaymentService::paymeTransaction($amount)]);


            default:
                return $this->fail([], 'Bad request');
        }
    }

}
