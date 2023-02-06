<?php

namespace App\Services\Chat;

use App\Models\ChMessage;
use App\Models\Task;
use App\Models\User;

class ContactService
{
    /**
     * @param $authUser
     * @return mixed
     */
    public static function contactsList($authUser): mixed
    {
        // get not deleted archive chat user ids
        $messages = ChMessage::query()->select('from_id', 'to_id', 'created_at','deleted_at')
            ->where('deleted_at',null)
            ->where('to_id', $authUser)
            ->orWhere('from_id', $authUser)
            ->orderByDesc('created_at')->distinct()->get()->toArray();
        $userIdsList = [];
        foreach ($messages as $message) {
            if ($message['from_id'] === $authUser) {
                $userIdsList[] = $message['to_id'];
            } else {
                $userIdsList[] = $message['from_id'];
            }
        }

        // get moderators
        foreach (User::query()
                     ->whereIn('role_id', [User::ROLE_ADMIN, User::ROLE_MODERATOR])
                     ->whereNotIn('id', $userIdsList)
                     ->distinct()->pluck('id') as $moderator_id) {
            $userIdsList[] = $moderator_id;
        }

        // get performers and users where task is on process status
        foreach (Task::query()
                     ->where('status', Task::STATUS_IN_PROGRESS)
                     ->where(function ($query) use ($authUser) {
                         $query->where('user_id', $authUser)
                             ->orWhere('performer_id', $authUser);
                     }) as $id) {
            $userIdsList[] = $id;
        }
        $userIdsList[] = setting('site.moderator_id');

        // get unique elements and remove current user from list
        $userIdsList = array_unique($userIdsList);
        if (($key = array_search($authUser, $userIdsList)) !== false) {
            unset($userIdsList[$key]);
        }

        return $userIdsList;
    }
}
