<?php

namespace App\Events;

use Illuminate\Support\Facades\Cache;

class CheckAndClearTopUsersCache
{
    public function handle(UserEvent $event)
    {
        $updatedUsers = $event->getUser();
        $users = Cache::get('users', []);
        foreach($users as $user) {
            if($updatedUsers->id == $user->id) {
                Cache::forget('users');
                return;
            }
        }
    }
}
