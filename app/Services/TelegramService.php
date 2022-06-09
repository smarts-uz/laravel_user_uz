<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    private string $token;
    private string $sendMessageApi;
    private string $group;

    public function __construct()
    {
        $this->token = setting('site.bot_token');
        $this->sendMessageApi = 'https://api.telegram.org/bot' . $this->token . '/sendMessage';
        $this->group = setting('site.channel_username');
    }

    public function sendMessage($data)
    {
        $id = $data['id'];
        $complaint = $data['complaint'];
        $userName = $data['user_name'];
        $taskName = $data['task_name'];
        $text = "```\n#{$id}\n\nUser: {$userName}\nTask: {$taskName}\n\n{$complaint}\n```";
        return Http::post($this->sendMessageApi, [
            'chat_id' => $this->group,
            'text' => $text,
            'parse_mode' => 'MarkdownV2'
        ]);
    }
}
