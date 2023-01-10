<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $gender
 */
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
        if((int)$this->gender === 1){
            $date_gender = __('Был онлайн');
        }else{
            $date_gender = __('Была онлайн');
        }
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        if ($this->last_seen >= $date) {
            $lastSeen = __('В сети');
        } else {
            $seenDate = Carbon::parse($this->last_seen);
            $seenDate->locale(app()->getLocale() . '-' . app()->getLocale());
            if(app()->getLocale()==='uz'){
                $lastSeen = $seenDate->diffForHumans().' saytda edi';
            }else{
                $lastSeen = $date_gender. $seenDate->diffForHumans();
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => asset('storage/'.$this->avatar),
            'phone_number' => correctPhoneNumber($this->phone_number),
            'degree' => $this->phone_number,
            'likes' => $this->review_good,
            'dislikes' => $this->review_bad,
            'stars' => $this->review_rating,
            'last_seen' => $lastSeen,
        ];
    }
}
