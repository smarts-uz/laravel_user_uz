<?php

namespace App\Http\Controllers\vendor\Chatify\Api;

use App\Http\Resources\MessageResource;
use App\Http\Resources\UserInSearchChatResource;
use App\Models\Chat\ChatifyMessenger;
use App\Services\Chat\ContactService;
use App\Services\CustomService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Chat\ChatifyMessenger as Chatify;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;


class MessagesController extends \Chatify\Http\Controllers\Api\MessagesController
{
    /**
     * @var Chatify
     */
    private Chatify $chatify;

    public function __construct()
    {
        $this->chatify = new Chatify();
    }

    /**
     * @OA\Post(
     *     path="/api/chat/deleteConversation",
     *     tags={"Chat"},
     *     summary="Chat Delete Conversation",
     *     @OA\Parameter (
     *          in="query",
     *          name="id",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     }
     * )
     */

    public function deleteConversation(Request $request)
    {
        return parent::deleteConversation($request);
    }

    /**
     * @OA\Post(
     *     path="/api/chat/sendMessage",
     *     tags={"Chat"},
     *     summary="",
     *     @OA\Parameter (
     *          in="query",
     *          name="id",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="msg",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function send(Request $request): JsonResponse
    {
        // default variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;
        $chatMessenger = new Chatify();
        /** @var User $auth_user */
        $auth_user = Auth::user();
        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = $chatMessenger->getAllowedImages();
            $allowed_files  = $chatMessenger->getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            // check file size
            if ($file->getSize() < $chatMessenger->getMaxUploadSize()) {
                if (in_array($file->getClientOriginalExtension(), $allowed)) {
                    // get attachment name
                    $attachment_title = $file->getClientOriginalName();
                    // upload attachment and store the new name
                    $attachment = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $file->move(public_path('/storage/' . config('chatify.attachments.folder')), $attachment);
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
            $this->chatify->newMessage([
                'id' => $messageID,
                'type' => 'user', //$request['type'],
                'from_id' => $auth_user->id,
                'to_id' => $request['id'],
                'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => ($attachment) ? json_encode((object)[
                    'new_name' => $attachment,
                    'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                ]) : null,
            ]);

            // fetch message to send it with the response
            $messageData = $chatMessenger->fetchMessage($messageID);

            // send to user using pusher
            $chatMessenger->push('private-chatify', 'messaging', [
                'from_id' => $auth_user->id,
                'to_id' => $request['id'],
                'message' => $chatMessenger->messageCard($messageData, 'default')
            ]);
            $locale = (new CustomService)->cacheLang($request['id']);
            NotificationService::pushNotification(User::query()->find($request['id']), [
                'title' => trans('Новое сообщение', [], $locale),
                'body' => trans('У вас новое сообщение от user', ['user' => Auth::user()->name], $locale)
            ], 'chat', $messageData ?? []);

            $user = User::query()->findOrFail(\auth()->id());
            $role = match ($user->role_id) {
                User::ROLE_PERFORMER => 'Performer',
                User::ROLE_USER => 'User',
                default => 'Admin',
            };
            if($request['id'] === setting('site.moderator_id')){
                $bot = new Nutgram(setting('chat.TELEGRAM_TOKEN'));
//            $send_message_text = setting('chat.send_message_text');
//            $message = strtr($send_message_text, [
//                '{message}'=> $request['message'],
//                '{name}'=> $user->name,
//                '{phone}'=>  $user->phone_number,
//                '{role}'=> $role,
//                '{link}'=> 'https://user.uz/chat/'.$user->id,
//            ]);
                $message = 'Xabar matni : ' . $request['message']. "\n" . 'Nomi: '. $user->name . "\n" . 'Telefon raqam: ' . $user->phone_number
                    . "\n" . 'Foydalanuvchi roli: '. $role . "\n" . 'Chat link: ' . 'https://user.uz/chat/'.$user->id;

                $bot->sendMessage($message, ['chat_id' => setting('chat.CHANNEL_ID')]);
            }
            return Response::json([
                'success' => true,
                'data' => $messageData ?? [],
                'message' => 'Success',
            ]);
        }

        return Response::json([
            'success' => false,
            'data' => $error['message'],
            'message' => 'Fail',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/chat/fetchMessages",
     *     tags={"Chat"},
     *     summary="Chat Fetch Messages",
     *     @OA\Parameter (
     *          in="query",
     *          name="id",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function fetch(Request $request): JsonResponse
    {
        $query = $this->chatify->fetchMessagesQuery($request['id']);
        $messages = $query->latest()->get();
        $this->chatify->makeSeen($request['id']);

        $messages = MessageResource::collection($messages);
        return Response::json([
            'success' => true,
            'data' => $messages,
            'message' => 'Success'
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/chat/makeSeen",
     *     tags={"Chat"},
     *     summary="",
     *     @OA\Parameter (
     *          in="query",
     *          name="id",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function seen(Request $request)
    {
        // make as seen
        $this->chatify->makeSeen($request['id']);
        // send the response
        return Response::json([
            'success' => true,
            'message' => 'Success',
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/chat/getContacts",
     *     tags={"Chat"},
     *     summary="Chat getContacts",
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     }
     * )
     */
    public function getContacts(Request $request): JsonResponse
    {
        $userIdsList = ContactService::contactsList(Auth::user()->id);

        $chatItem = new ChatifyMessenger();
        if (count($userIdsList) > 0) {
            $contacts = [];
            foreach ($userIdsList as $userId) {
                $user = User::query()->find($userId);
                if ($user) {
                    $contacts[] = $chatItem->getContactItemApi($user);
                }
            }
        } else {
            $contacts = '<p class="message-hint center-el"><span>Your contact list is empty</span></p>';
        }

        return Response::json([
            'success' => true,
            'data' => ['contacts' => $contacts],
            'message' => 'Success'
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/chat/search",
     *     tags={"Chat"},
     *     summary="Chat search",
     *     @OA\Parameter (
     *          in="query",
     *          name="name",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     }
     * )
     */

    public function search(Request $request): JsonResponse
    {
        $input = trim(filter_var($request['name'], FILTER_SANITIZE_STRING));
        $ids = ContactService::contactsList(Auth::user()->id);
        if (($key = array_search(Auth::id(), $ids)) !== false) {
            unset($ids[$key]);
        }
        $records = User::query()
            ->select('id', 'name', 'active_status', 'avatar', 'last_seen')
            ->whereIn('id',$ids)
            ->where('name', 'LIKE', "%{$input}%")
            ->get();
        return Response::json([
            'success' => true,
            'data' => UserInSearchChatResource::collection($records),
            'message' => 'Success'
        ]);
    }
}
