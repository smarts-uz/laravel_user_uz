<?php

namespace App\Http\Resources;

use App\Models\BlockedUser;
use App\Services\CustomService;
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
        if((int)$this->gender === 1){
            $date_gender = __('Был онлайн');
        }else{
            $date_gender = __('Была онлайн');
        }
        if ($this->last_seen >= Carbon::now()->subMinutes(2)->toDateTimeString()) {
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
        $user_exists = BlockedUser::query()->where('user_id',auth()->id())->where('blocked_user_id',$this->id)->exists();
        if(!$user_exists){
            $user_avatar = asset('storage/'.$this->avatar);
        }else{
            $user_avatar = asset("images/block-user.jpg");
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $user_avatar,
            'phone_number' => (new CustomService)->correctPhoneNumber($this->phone_number),
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
