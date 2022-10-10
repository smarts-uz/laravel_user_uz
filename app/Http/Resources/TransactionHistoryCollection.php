<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionHistoryCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user_id" => $this->transactionable_id,
            "method" => ucfirst($this->payment_system) == 'Task' ? __('Оплата за отклик') : ucfirst($this->payment_system),
            "amount" => ucfirst($this->payment_system) == 'Paynet' ? $this->amount / 100 : $this->amount,
            "status" => strtolower($this->payment_system) == 'task' ? 0 : 1,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "state" => $this->state
        ];
    }
}
