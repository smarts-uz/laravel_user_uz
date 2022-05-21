<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $goods = $this->review_good;
        $bads =  $this->review_bad;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar?asset('storage/'.$this->avatar):null,
            'phone_number' => $this->phone_number,
            'degree' => $this->phone_number,
            'likes' => $goods,
            'dislikes' => $bads,
            'stars' => round($goods * 5 / (($goods+$bads==0) ? 1 : ($goods + $bads))),
            'last_seen' => $this->last_seen_at,
        ];
    }
}
