<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockUserListResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this->user;
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        if((int)$user->gender === 1){
            $date_gender = __('Был онлайн');
        }else{
            $date_gender = __('Была онлайн');
        }
        if ($user->last_seen >= $date) {
            $lastSeen = __('В сети');
        } else {
            $seenDate = Carbon::parse($user->last_seen);
            $seenDate->locale(app()->getLocale() . '-' . app()->getLocale());
            if(app()->getLocale()==='uz'){
                $lastSeen = $seenDate->diffForHumans().' saytda edi';
            }else{
                $lastSeen = $date_gender. $seenDate->diffForHumans();
            }
        }
        return [
            'id' => $this->id,
            'user_id'=> $this->user_id,
            'blocked_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'last_seen' => $lastSeen,
                'avatar' => asset('storage/' . $user->avatar),
            ],
            'created_at' => $this->created_at
        ];
    }
}
