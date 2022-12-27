<?php

namespace App\Models\Chat;

use App\Http\Resources\MessageResource;
use Carbon\Carbon;

class ChatifyMessenger extends \Chatify\ChatifyMessenger
{

    public $pusher;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Authentication for pusher
     *
     * @param string $channelName
     * @param string $socket_id
     * @param null $data
     * @return mixed
     */
    public function pusherAuth($channelName, $socket_id, $data = null)
    {
        return $this->pusher->socketAuth($channelName, $socket_id, $data);
    }


    public function getContactItemApi($user)
    {
        // get last message
        $lastMessage = $this->getLastMessageQuery($user->id);
        // Get Unseen messages counter
        $unseenCounter = $this->countUnseenMessages($user->id);

        if((int)$user->gender === 1){
            $date_gender = __('Был онлайн');
        }else{
            $date_gender = __('Была онлайн');
        }
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        if ($user->last_seen >= $date) {
            $lastSeen = __('В сети');
        } else {
            $seenDate = Carbon::parse($user->last_seen);
            $seenDate->locale(app()->getLocale() . '-' . app()->getLocale());
            if(app()->getLocale()==='uz'){
                $lastSeen = $seenDate->diffForHumans().' onlayn edi';
            }else{
                $lastSeen = $date_gender. $seenDate->diffForHumans();
            }
        }
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'active_status' => $user->active_status,
                'avatar' => url('/storage') . '/' . $user->avatar,
                'last_seen' => $lastSeen
            ],
            'lastMessage' => $lastMessage ? MessageResource::collection(collect([$lastMessage])) : [],
            'unseenCounter' => $unseenCounter,
        ];
    }
}
