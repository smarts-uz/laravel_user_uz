<?php

namespace App\Models\Chat;

use App\Http\Resources\MessageResource;

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
     * @param array $data
     * @return void
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

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'active_status' => $user->active_status,
                'avatar' => url('/storage') . '/' . $user->avatar,
                'last_seen' => $user->last_seen
            ],
            'lastMessage' => $lastMessage ? MessageResource::collection(collect([$lastMessage])) : [],
            'unseenCounter' => $unseenCounter,
        ];
    }
}
