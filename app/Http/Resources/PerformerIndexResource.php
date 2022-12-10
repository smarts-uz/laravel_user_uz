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
        if ($this->last_seen >= Carbon::now()->subMinutes(2)->toDateTimeString()) {
            $lastSeen = __('В сети');
        } else {
            $seenDate = Carbon::parse($this->last_seen);
            $seenDate->locale(app()->getLocale() . '-' . app()->getLocale());
            $lastSeen = $seenDate->diffForHumans();
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar ? asset('storage/'.$this->avatar) : null,
            'phone_number' => correctPhoneNumber($this->phone_number),
            'location' => $this->location,
            'last_seen' => $lastSeen,
            'likes' => $this->review_good,
            'dislikes' => $this->review_bad,
            'description' => $this->description,
            'stars' => $this->review_rating,
            'role_id' => $this->role_id,
            'views' => $this->performer_views()->count(),
        ];
    }
}
