<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInSearchChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
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
                $lastSeen = $seenDate->diffForHumans().' onlayn edi';
            }else{
                $lastSeen = $date_gender. $seenDate->diffForHumans();
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active_status' => $this->active_status,
            'avatar' => url('/storage') . '/' . $this->avatar,
            'last_seen' => $lastSeen
        ];
    }
}
