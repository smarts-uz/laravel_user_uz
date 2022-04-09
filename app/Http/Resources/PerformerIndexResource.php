<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformerIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $goods = $this->goodReviews()->count();
        $bads =  $this->badReviews()->count();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar?asset('storage/'.$this->avatar):null,
            'phone_number' => $this->phone_number,
            'location' => $this->location,
            'last_seen' => Carbon::parse( $this->last_seen)->diffForHumans(),
            'likes' => $goods,
            'dislikes' => $bads,
            'stars' => round($goods * 5 / (($goods+$bads==0) ? 1 : ($goods + $bads))),
        ];
    }
}
