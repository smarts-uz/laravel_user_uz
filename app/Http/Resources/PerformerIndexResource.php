<?php

namespace App\Http\Resources;

use App\Models\BlockedUser;
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
            if(app()->getLocale()==='uz'){
                $lastSeen = $seenDate->diffForHumans().' saytda edi';
            }else{
                $lastSeen = __('Был онлайн'). $seenDate->diffForHumans();
            }
        }
        $user_exists = BlockedUser::query()->where('user_id',auth()->id())->where('blocked_user_id',$this->id)->exists();
        if(!$user_exists){
            $blocked_user = 0;
        }else{
            $blocked_user = 1;
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
            'blocked_user'=> $blocked_user,
        ];
    }
}
