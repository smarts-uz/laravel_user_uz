<?php

namespace App\Http\Controllers\vendor\Chatify;


use App\Models\Chat\ChatifyMessenger as Chatify;
use App\Models\ChMessage;
use App\Models\User;
use App\Services\Chat\ContactService;
use App\Services\CustomService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MessagesController extends \Chatify\Http\Controllers\MessagesController
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
     * Authinticate the connection for pusher
     *
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\Response
     */

    public function pusherAuth(Request $request): \Illuminate\Http\Response|JsonResponse
    {
        // Auth data
        /** @var User $user */
        $user =  Auth::user();
        $authData = json_encode([
            'user_id' => $user->id,
            'user_info' => [
                'name' =>$user->name
            ]
        ]);
        // check if user authorized
        if (Auth::check()) {
            $auth =  $this->chatify->pusherAuth($request['channel_name'], $request['socket_id'], $authData);
            return \response($auth, 200);
        }
        // if not authorized
        return Response::json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Fetch data by id for (user/group)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function idFetchData(Request $request): JsonResponse
    {
        // Favorite
        $favorite = $this->chatify->inFavorite($request['id']);

        // User data
        if ($request['type'] == 'user') {
            $fetch = User::where('id', $request['id'])->first();
            if ($fetch) {
                $userAvatar = asset('/storage/' . config('chatify.user_avatar.folder') . '/' . $fetch->avatar);
            }
        }

        // send the response
        return Response::json([
            'favorite' => $favorite,
            'fetch' => $fetch ?? [],
            'user_avatar' => $userAvatar ?? null,
        ]);
    }

    /**
     * Send a message to database
     *
     * @param Request $request
     * @return JsonResponse response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = $this->chatify->getAllowedImages();
            $allowed_files = $this->chatify->getAllowedFiles();
            $allowed = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            // check file size
            if ($file->getSize() < $this->chatify->getMaxUploadSize()) {
                if (in_array($file->getClientOriginalExtension(), $allowed)) {
                    // get attachment name
                    $attachment_title = $file->getClientOriginalName();
                    // upload attachment and store the new name
                    $attachment = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $file->move("storage/" . config('chatify.attachments.folder'), $attachment);
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
                'type' => $request['type'],
                'from_id' => Auth::id(),
                'to_id' => $request['id'],
                'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => ($attachment) ? json_encode((object)[
                    'new_name' => $attachment,
                    'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                ]) : null,
            ]);

            // fetch message to send it with the response
            $messageData = $this->chatify->fetchMessage($messageID);

            // send to user using pusher
            $this->chatify->push('private-chatify', 'messaging', [
                'from_id' => Auth::id(),
                'to_id' => $request['id'],
                'message' => $this->chatify->messageCard($messageData, 'default')
            ]);
            $locale = (new CustomService)->cacheLang($request['id']);
            NotificationService::pushNotification(User::query()->find($request['id']), [
                'title' => trans('Новое сообщение', [], $locale),
                'body' => trans('У вас новое сообщение от user', ['user' => Auth::user()->name], $locale)
            ], 'chat', $messageData ?? []);
        }
        if (session('lang') === 'ru'){
            $locale = 'ru';
        }else{
            $locale = 'uz';
        }
        $request_id = $request['id'];
        $message = $request['message'];
        $AuthId = auth()->id();
        (new ContactService)->telegramNotification($locale, $request_id, $message, $AuthId);

        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error,
            'message' => $this->chatify->messageCard($messageData),
            'tempID' => $request['temporaryMsgId'],
        ]);
    }

    /**
     * fetch [user/group] messages from database
     *
     * @param Request $request
     * @return JsonResponse response
     */
    public function fetch(Request $request): JsonResponse
    {
        $query = $this->chatify->fetchMessagesQuery($request['id'])->latest();
        $messages = $query->paginate($request->per_page ?? $this->perPage);
        $this->chatify->makeSeen($request['id']);
        $totalMessages = $messages->total();
        $lastPage = $messages->lastPage();
        $response = [
            'total' => $totalMessages,
            'last_page' => $lastPage,
            'last_message_id' => collect($messages->items())->last()->id ?? null,
            'messages' => '',
        ];

        // if there is no messages yet.
        if ($totalMessages < 1) {
            $response['messages'] = '<p class="message-hint center-el"><span>Say \'hi\' and start messaging</span></p>';
            return Response::json($response);
        }
        if (count($messages->items()) < 1) {
            $response['messages'] = '';
            return Response::json($response);
        }
        $allMessages = null;
        foreach ($messages->reverse() as $message) {
            $allMessages .= $this->chatify->messageCard(
                $this->chatify->fetchMessage($message->id)
            );
        }
        $response['messages'] = $allMessages;
        return Response::json($response);
    }

    /**
     * Make messages as seen
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function seen(Request $request): JsonResponse
    {
        // make as seen
        $seen = $this->chatify->makeSeen($request['id']);
        // send the response
        return Response::json([
            'status' => $seen,
        ], 200);
    }

    /**
     * Get contacts list
     *
     * @param Request $request
     * @return JsonResponse response
     */
    public function getContacts(Request $request): JsonResponse
    {
        $userIdsList = ContactService::contactsList(Auth::user()->id);
        $chatItem = new Chatify();
        if (count($userIdsList) > 0) {
            $contacts = '';
            foreach ($userIdsList as $userId) {
                $user = User::query()->find($userId);
                if ($user) {
                    $contacts .= $chatItem->getContactItem($user);
                }
            }
        } else {
            $contacts = '<p class="message-hint center-el"><span>Your contact list is empty</span></p>';
        }

        return Response::json([
            'contacts' => $contacts,
            'total' => 1,
            'last_page' => 1,
        ]);
    }

    /**
     * Get favorites list
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getFavorites(Request $request): JsonResponse
    {
        // send the response
        return Response::json([
            'count' => 0,
            'favorites' => 0,
        ]);
    }

    /**
     * Update user's list item data
     *
     * @param Request $request
     * @return JsonResponse response
     */
    public function updateContactItem(Request $request): JsonResponse
    {
        // Get user data
        $user = User::where('id', $request['user_id'])->first();
        if (!$user) {
            return Response::json([
                'message' => 'User not found!',
            ], 401);
        }
        $contactItem = $this->chatify->getContactItem($user);

        // send the response
        return Response::json([
            'contactItem' => $contactItem,
        ], 200);
    }


    /**
     * Search in messenger
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $getRecords = null;
        $input = trim(filter_var($request['input'], FILTER_SANITIZE_STRING));
        $ids = ContactService::contactsList(Auth::user()->id);
        if (($key = array_search(Auth::id(), $ids)) !== false) {
            unset($ids[$key]);
        }
        $records = User::query()
            ->select('id', 'name', 'active_status', 'avatar', 'last_seen')
            ->whereIn('id',$ids)
            ->where('name', 'LIKE', "%{$input}%")
            ->paginate($request->per_page ?? $this->perPage);
        foreach ($records->items() as $record) {
            $getRecords .= view('Chatify::layouts.listItem', [
                'get' => 'search_item',
                'type' => 'user',
                'user' => $record,
            ])->render();
        }
        if ($records->total() < 1) {
            $getRecords = '<p class="message-hint center-el"><span>Nothing to show.</span></p>';
        }
        // send the response
        return Response::json([
            'records' => $getRecords,
            'total' => $records->total(),
            'last_page' => $records->lastPage()
        ], 200);
    }

    /**
     * Get shared photos
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sharedPhotos(Request $request): JsonResponse
    {
        $shared = $this->chatify->getSharedPhotos($request['user_id']);
        $sharedPhotos = null;

        // shared with its template
        for ($i = 0, $iMax = count($shared); $i < $iMax; $i++) {
            $sharedPhotos .= view('Chatify::layouts.listItem', [
                'get' => 'sharedPhoto',
                'image' => asset('storage/attachments/' . $shared[$i]),
            ])->render();
        }
        // send the response
        return Response::json([
            'shared' => count($shared) > 0 ? $sharedPhotos : '<p class="message-hint"><span>Nothing shared yet</span></p>',
        ], 200);
    }

    /**
     * Get Unseen messages count
     *
     * @return int
     */
    public static function unseenCount(): int
    {
        return ChMessage::query()->where('to_id', Auth::id())->where('seen', 0)->count();
    }
}
