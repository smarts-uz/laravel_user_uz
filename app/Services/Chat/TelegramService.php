<?php

namespace App\Services\Chat;


use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;
use function Clue\StreamFilter\fun;

class TelegramService
{
    public Nutgram $nutgram;

    public function __construct()
    {
        $this->nutgram = new Nutgram(env('GROUP_BOT_TOKEN'));
    }

    public function web()
    {
        $this->nutgram->setRunningMode(Webhook::class);
        $this->handle($this->nutgram);
    }

    public function handle(Nutgram $bot): void
    {
        $bot->onChannelPost(function (Nutgram $bot) {
            sleep(3);

            $pin = $bot->getChat(-1001851760117)->pinned_message->message_id;
            file_put_contents('pin.json', json_encode($pin, JSON_THROW_ON_ERROR), 8);
        });


        $bot->run();
    }


}
