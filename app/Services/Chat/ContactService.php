<?php

namespace App\Services\Chat;

use App\Models\ChMessage;
use App\Models\Task;
use App\Models\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;

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

    /**
     * @param $locale
     * @param $request_id
     * @param $message
     * @param $AuthId
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function telegramNotification($locale, $request_id, $message, $AuthId): void
    {
        $admins = User::query()->findOrFail($request_id);
        if($admins->hasPermission('admin_notifications')){
            $bot = new Nutgram(setting('chat.TELEGRAM_TOKEN'));
            if ($locale === 'ru'){
                $send_message_text = setting('chat.send_message_text_ru');
            }else{
                $send_message_text = setting('chat.send_message_text_uz');
            }
            $user = User::query()->findOrFail($AuthId);
            $role = match ($user->role_id) {
                User::ROLE_PERFORMER => 'Performer',
                User::ROLE_USER => 'User',
                User::ROLE_MODERATOR => 'Moderator',
                default => 'Admin',
            };
            $message = strtr($send_message_text, [
                '{message}'=> $message,
                '{name}'=> $user->name,
                '{phone}'=>  $user->phone_number,
                '{role}'=> $role,
                '{link}'=> 'https://user.uz/chat/'.$user->id,
            ]);
            $bot->sendMessage($message, ['chat_id' => setting('chat.CHANNEL_ID')]);
        }
    }
}
