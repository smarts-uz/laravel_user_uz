<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        $goods = $this->goodReviews()->count();
        $bads = $this->badReviews()->count();
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        if ($this->last_seen >= $date) {
            $lastSeen = 'online';
        } else {
            $seenDate = Carbon::parse($this->last_seen);
            $seenDate->locale($this->locale);
            $lastSeen = $seenDate->diffForHumans();
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => asset('storage/'.$this->avatar),
            'phone_number' => $this->phone_number,
            'degree' => $this->phone_number,
            'likes' => $goods,
            'dislikes' => $bads,
            'stars' => round($goods * 5 / (($goods+$bads==0) ? 1 : ($goods + $bads))),
            'last_seen' => $lastSeen,
        ];
    }
}
