<?php

namespace Modules\SupportChat\Http\Controllers;

use App\Models\ChMessage;
use App\Models\User;
use Illuminate\Routing\Controller;
use SergiX44\Nutgram\Nutgram;

class TelegramController extends Controller
{
    /**
     * Handle the telegram webhook request.
     */
    public function __invoke(Nutgram $bot)
    {
        $bot->onMessage(function (Nutgram $bot) {
            if ($bot->message()->sender_chat) {
                if ($bot->message()->sender_chat->id == setting('site.CHANNEL_ID')) {
                    if (is_array(explode(' ', $bot->message()->text))) {
                        $list = explode(' ', $bot->message()->text);
                        $user = User::where('phone_number', $list[count($list) - 1])->first();
                        $user->update(['message_id' => $bot->message()->message_id]);
                    }
                }
            }

            if ($bot->message()->reply_to_message) {
                if($bot->message()->reply_to_message->entities){
                    if ($bot->message()->reply_to_message->sender_chat->id == setting('site.CHANNEL_ID')) {
                        $message = $bot->message()->reply_to_message->text;
                        $list = explode(' ', $message);
                        $user = User::where('phone_number', $list[count($list) - 1])->first();

                        $messageID = mt_rand(9, 999999999) + time();
                        ChMessage::create([
                            'id' => $messageID,
                            'type' => 'user',
                            'from_id' => setting("site.admin_id"),
                            'to_id' => $user->id,
                            'body' => $bot->message()->text,
                            'seen' => 0,
                        ]);
                    }
                }
            }
        });

        $bot->run();
    }
}
