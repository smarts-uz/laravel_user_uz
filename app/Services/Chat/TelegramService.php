<?php

namespace App\Services\Chat;


use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use TCG\Voyager\Models\User;

class TelegramService
{
    public Nutgram $nutgram;

    public ContactService $contact_service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->nutgram = new Nutgram(setting('chat.GROUP_BOT_TOKEN','5544065580:AAHDQbKESXvfNbaLK5asZ8LmF03jYSo992o'));
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
