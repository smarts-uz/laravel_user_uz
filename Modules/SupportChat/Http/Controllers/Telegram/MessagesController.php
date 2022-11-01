<?php

namespace Modules\SupportChat\Http\Controllers\Telegram;

use App\Models\ChMessage;
use App\Models\User;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;

class MessagesController extends Chatify
{
    public function send(Request $request)
    {
        // default variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();
            $allowed_files  = Chatify::getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            // check file size
            if ($file->getSize() < Chatify::getMaxUploadSize()) {
                if (in_array(strtolower($file->getClientOriginalExtension()), $allowed)) {
                    // get attachment name
                    $attachment_title = $file->getClientOriginalName();
                    // upload attachment and store the new name
                    $attachment = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $file->storeAs(config('supportchat.attachments.folder'), $attachment, config('chatify.storage_disk_name'));
                } else {
                    $error->status = 1;
                    $error->message = "File extension not allowed!";
                }
            } else {
                $error->status = 1;
                $error->message = "File size you are trying to upload is too large!";
            }
        }

        if (!$error->status) {
            // send to database
            $messageID = mt_rand(9, 999999999) + time();
            Chatify::newMessage([
                'id' => $messageID,
                'type' => $request['type'],
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => ($attachment) ? json_encode((object)[
                    'new_name' => $attachment,
                    'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                ]) : null,
            ]);

            $message = ChMessage::where('id', $messageID)->first();
            $user = User::where('id', $message->from_id)->first();
            $bot = new Nutgram(setting('chat.TELEGRAM_TOKEN'));

            if ($request->file('file')) {
                switch (true) {
                    case strlen($message->body) == 0:
                        $fl = fopen('storage/attachments/' . $attachment, 'r+');
                        $bot->sendDocument($fl, ['chat_id' => setting('chat.CHAT_ID'), 'reply_to_message_id' => $user->message_id]);
                        break;
                    case strlen($message->body) != 0:
                        $fl = fopen('storage/attachments/' . $attachment, 'r+');
                        $bot->sendDocument($fl, ['chat_id' => setting('chat.CHAT_ID'), 'caption' => $message->body, 'reply_to_message_id' => $user->message_id]);
                        break;
                    default :
                        echo 'hello';
                        break;
                }
            } else {
                $bot->sendMessage($message->body, ['chat_id' => setting('chat.CHAT_ID'), 'reply_to_message_id' => $user->message_id]);
            }


            // fetch message to send it with the response
            $messageData = Chatify::fetchMessage($messageID);

            // send to user using pusher
            Chatify::push('private-chatify', 'messaging', [
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'message' => Chatify::messageCard($messageData, 'default')
            ]);
        }

        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error,
            'message' => Chatify::messageCard($messageData),
            'tempID' => $request['temporaryMsgId'],
        ]);
    }

    public function webhook(Nutgram $bot)
    {

        $bot->onMessageType(MessageTypes::DOCUMENT, function (Nutgram $bot) {
            if($bot->message()->reply_to_message){
                if ($bot->message()->reply_to_message->sender_chat->id == setting('chat.CHANNEL_ID')) {
                    $bot->getFile($bot->message()->document->file_id)->save('storage/attachments/' . $bot->message()->document->file_name);
                    $message = $bot->message()->reply_to_message->text;
                    $list = explode(' ', $message);
                    $user = User::where('phone_number', $list[count($list) - 1])->first();
                    Chatify::newMessage([
                        'id' => mt_rand(9, 999999999) + time(),
                        'type' => 'user',
                        'from_id' => setting("chat.admin_id"),
                        'to_id' => $user->id,
                        'body' => $bot->message()->text,
                        'seen' => 0,
                        'tg_message_id' => $bot->message()->message_id,
                        'attachment' => ($bot->message()->document->file_name) ? json_encode((object)[
                            'new_name' => $bot->message()->document->file_name,
                            'old_name' => htmlentities(trim($bot->message()->document->file_name), ENT_QUOTES, 'UTF-8'),
                        ]) : null,
                    ]);
                }
            }
        });

        $bot->onEditedMessage(function (Nutgram $bot) {
            $message = ChMessage::where('tg_message_id', $bot->message()->message_id)->first();
            $message_text = $message->body;
            $message->update([
                'edittext' => $message_text,
                'body' => $bot->message()->text
            ]);
        });

        $bot->onMessage(function (Nutgram $bot) {

            switch (true) {
                case $bot->message()->sender_chat:
                    if ($bot->message()->sender_chat->id == setting('chat.CHANNEL_ID')) {
                        $list = explode(' ', $bot->message()->text);
                        $user = User::where('phone_number', $list[count($list) - 1])->first();
                        $user->update(['message_id' => $bot->message()->message_id]);
                    }
                    break;
                case $bot->message()->reply_to_message:
                    if ($bot->message()->reply_to_message->entities) {
                        if ($bot->message()->reply_to_message->sender_chat->id == setting('chat.CHANNEL_ID')) {
                            $message = $bot->message()->reply_to_message->text;
                            $list = explode(' ', $message);
                            $user = User::where('phone_number', $list[count($list) - 1])->first();
                            ChMessage::create([
                                'id' => mt_rand(9, 999999999) + time(),
                                'type' => 'user',
                                'from_id' => setting("chat.admin_id"),
                                'to_id' => $user->id,
                                'body' => $bot->message()->text,
                                'seen' => 0,
                                'tg_message_id' => $bot->message()->message_id
                            ]);
                        }
                    }
                    break;
                default :
                    echo 'hello';
                    break;
            }
        });

        $bot->run();
    }
}
