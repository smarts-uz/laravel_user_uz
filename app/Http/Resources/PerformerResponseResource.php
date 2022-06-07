<?php

namespace App\Http\Resources;

use App\Models\TaskResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformerResponseResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $performer = $this->performer;
        $goods = $performer->review_good;
        $bads =  $performer->review_bad;
        return [
            'id' => $performer->id,
            'name' => $performer->name,
            'avatar' => $performer->avatar?asset('storage/'.$this->avatar):null,
            'phone_number' => $performer->phone_number,
            'degree' => $performer->phone_number,
            'likes' => $goods,
            'dislikes' => $bads,
            'stars' => round($goods * 5 / (($goods+$bads==0) ? 1 : ($goods + $bads))),
            'last_seen' => $performer->last_seen_at,
            'price' => $this->price,
            'description' => $this->description,
            'created_at' => $this->created
        ];
    }
}
