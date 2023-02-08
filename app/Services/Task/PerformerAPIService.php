<?php

namespace App\Services\Task;


use App\Http\Resources\PerformerIndexResource;
use App\Models\BlockedUser;
use App\Models\User;
use Carbon\Carbon;

class PerformerAPIService
{
    public function service($online, $per_page)
    {
        $performers = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->orderByDesc('review_rating')
            ->orderByRaw('(review_good - review_bad) DESC');
        if (isset($online))
        {
            $date = Carbon::now()->subMinutes(2)->toDateTimeString();
            $performers = $performers->where('role_id', User::ROLE_PERFORMER)->where('last_seen', ">=",$date);
        }

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
        $performer = $performers->paginate($per_page);
        if(!empty( $performer)) {
            $data = [
                'id' => $performer->id,
                'name' => $performer->name,
                'email' => $performer->email,
                'avatar' => $user_avatar,
                'phone_number' => (!empty($performer->phone_number)) ? $this->correctPhoneNumber($performer->phone_number) : '',
                'location' => $performer->location,
                'last_seen' => $lastSeen,
                'likes' => $performer->review_good,
                'dislikes' => $performer->review_bad,
                'description' => $performer->description,
                'stars' => $performer->review_rating,
                'role_id' => $performer->role_id,
                'views' => $performer->performer_views()->count(),
            ];
        }  else {
            $data = [];
        }

        return ['data' => $data];
    }

    function correctPhoneNumber($phone)
    {
        return match (true) {
            strlen($phone) == 12 => '+' . $phone,
            strlen($phone) > 13 => substr($phone, 0, 13),
            default => $phone,
        };
    }
}
