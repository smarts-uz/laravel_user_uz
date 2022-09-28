<?php

namespace App\Http\Resources;

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
        return [
            'id' => $performer->id,
            'name' => $performer->name,
            'avatar' => $performer->avatar?asset('storage/'.$performer->avatar):null,
            'phone_number' => $performer->phone_number,
            'degree' => $performer->phone_number,
            'likes' => $performer->review_good,
            'dislikes' => $performer->review_bad,
            'stars' => $performer->review_rating,
            'last_seen' => $performer->last_seen_at,
            'price' => $this->price,
            'description' => $this->description,
            'created_at' => $this->created
        ];
    }
}
