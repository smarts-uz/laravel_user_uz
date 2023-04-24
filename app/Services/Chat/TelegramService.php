<?php

namespace App\Services\Chat;


use App\Models\ChMessage;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;
use TCG\Voyager\Models\User;
use function Clue\StreamFilter\fun;

class TelegramService
{
    public Nutgram $nutgram;

    public ContactService $contact_service;

    public function __construct()
    {
        $this->nutgram = new Nutgram(env('GROUP_BOT_TOKEN'));
        $this->contact_service = new ContactService();
    }

    public function web()
    {
        $this->nutgram->setRunningMode(Webhook::class);
        $this->handle($this->nutgram);
    }

    public function handle(Nutgram $bot): void
    {
        $bot->onMessage(function(Nutgram $bot){
            $message = $bot->message();
            if ($message?->reply_to_message !== null){
                $user = User::where('discussion_post_id', $message?->reply_to_message->message_id);
                if ($user->exists()){
                    $user = $user->first();
                    $this->contact_service->sendFromTelegram($user->id, $message->text);
                }
            }else if ($message->sender_chat !== null){
                $user = User::where('post_id', $message->forward_from_message_id);
                $user->update([
                    "discussion_post_id" => $message->message_id
                ]);

                $user = $user->first();

                $bot->sendMessage($user->reply_message, [
                    "reply_to_message_id" => $message->message_id
                ]);
            }
        });

        $bot->run();
    }


}
