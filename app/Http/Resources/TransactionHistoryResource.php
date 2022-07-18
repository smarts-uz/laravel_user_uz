<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user_id" => $this->transactionable_id,
            "method" => ucfirst($this->payment_system),
            "amount" => ucfirst($this->payment_system) == 'Paynet' ? $this->amount / 100 : $this->amount,
            "status" => $this->state == 2 ? 1 : 0,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "state" => $this->state
        ];
    }
}
