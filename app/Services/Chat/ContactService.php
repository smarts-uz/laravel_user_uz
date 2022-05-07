<?php

namespace App\Services\Chat;

use App\Models\Chat\ChatifyMessenger;
use App\Models\Chat\ChMessage;
use App\Models\User;

class ContactService
{
    public static function contactsList($authUser)
    {
        $messages = ChMessage::query()->select('from_id', 'to_id', 'created_at')
            ->where('to_id', auth()->id())
            ->orWhere('from_id', \auth()->id())
            ->orderByDesc('created_at')->distinct()->get()->toArray();
        $userIdsList = [];
        foreach ($messages as $message) {
            if ($message['from_id'] == $authUser->id) {
                $userIdsList[] = $message['to_id'];
            } else {
                $userIdsList[] = $message['from_id'];
            }
        }
        foreach (User::query()
                     ->whereIn('role_id', [1, 6])
                     ->whereNotIn('id', $userIdsList)
                     ->distinct()->pluck('id') as $moderator_id) {
            $userIdsList[] = $moderator_id;
        }

        $userIdsList = array_unique($userIdsList);
        if (($key = array_search($authUser->id, $userIdsList)) !== false) {
            unset($userIdsList[$key]);
        }

        return $userIdsList;
    }
}
