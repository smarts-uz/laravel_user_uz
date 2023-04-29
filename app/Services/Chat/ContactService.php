<?php

namespace App\Services\Chat;

use App\Models\Chat\ChatifyMessenger as Chatify;
use App\Models\{ChMessage, Task, User};
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};
use Exception;
use JsonException;
use SergiX44\Nutgram\Nutgram;

class ContactService
{
    private Chatify $chatify;

    public function __construct()
    {
        $this->chatify = new Chatify();
    }

    /**
     * Bu method chatdagi userlarning ro'yxatini $authUserga qarab qaytaradi
     * @param $authUser
     * @return mixed
     */
    public static function contactsList($authUser): mixed
    {
        // get not deleted archive chat user ids
        $messages = ChMessage::query()->select('from_id', 'to_id', 'created_at', 'deleted_at')
            ->where('deleted_at', null)
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
        $userIdsList[] = setting('site.moderator_id', 1);

        // get unique elements and remove current user from list
        $userIdsList = array_unique($userIdsList);
        if (($key = array_search($authUser, $userIdsList)) !== false) {
            unset($userIdsList[$key]);
        }

        return $userIdsList;
    }

    /**
     * Bu method user 'admin_notifications' permissioni berilgan adminga yozsa, telegramga bildirishnoma yuboradi
     * @param $locale
     * @param $request_id
     * @param $ch_message
     * @param $AuthId
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function telegramNotification($locale, $request_id, $ch_message, $AuthId): void
    {
        $admins = User::query()->findOrFail($request_id);
        if ($admins->hasPermission('admin_notifications')) {
            $bot = new Nutgram(setting('chat.TELEGRAM_TOKEN', '5743173293:AAF33GAKELp-Id9y00EhIJRrpWI37umZ788'));
            if ($locale === 'ru') {
                $send_message_text = setting('chat.send_message_text_ru', '');
            } else {
                $send_message_text = setting('chat.send_message_text_uz', '');
            }

            $user = User::query()->findOrFail($AuthId);
            $role = match ($user->role_id) {
                User::ROLE_PERFORMER => 'Performer',
                User::ROLE_USER => 'User',
                User::ROLE_MODERATOR => 'Moderator',
                default => 'Admin',
            };

            if ($user->discussion_post_id === null) {
                $message = strtr($send_message_text, [
                    '{name}' => $user->name,
                    '{phone}' => $user->phone_number,
                    '{email}' => $user->email,
                    '{role}' => $role,
                    '{link}' => 'https://user.uz/chat/' . $user->id,
                ]);
                $message = $bot->sendMessage($message, ['chat_id' => setting('chat.CHANNEL_ID', '-1001548386291')]);
                User::where('id', $AuthId)->update([
                    "post_id" => $message->message_id,
                    "reply_message" => $ch_message
                ]);
            } else {
                $reply_id = User::where('id', $AuthId)->value('discussion_post_id');

                $bot->sendMessage($ch_message, ['chat_id' => setting('chat.GROUP_ID', '-1001852856557'), "reply_to_message_id" => $reply_id]);

            }

        }
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function sendFromTelegram($user_id, $message, $attachment = null): void
    {

        $messageID = random_int(9, 999999999) + time();
        $this->chatify->newMessage([
            'id' => $messageID,
            'type' => 'user',
            'from_id' => setting('site.moderator_id', 1),
            'to_id' => $user_id,
            'body' => htmlentities(trim($message), ENT_QUOTES, 'UTF-8'),
            'attachment' => ($attachment) ? json_encode((object)[
                'new_name' => $attachment,
                'old_name' => htmlentities(trim($attachment_title = ''), ENT_QUOTES, 'UTF-8'),
            ], JSON_THROW_ON_ERROR) : null,
        ]);
    }
}
