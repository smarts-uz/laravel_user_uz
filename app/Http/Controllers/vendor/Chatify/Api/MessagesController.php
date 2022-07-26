<?php

namespace App\Http\Controllers\vendor\Chatify\Api;

use App\Http\Resources\MessageResource;
use App\Http\Resources\UserInSearchChatResource;
use App\Models\Chat\ChatifyMessenger;
use App\Services\Chat\ContactService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Chatify\Facades\ChatifyMessenger as Chatify;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class MessagesController extends Controller
{
    protected $perPage = 30;


    public function download($fileName)
    {
        $path = storage_path() . '/app/public/' . config('chatify.attachments.folder') . '/' . $fileName;
        if (file_exists($path)) {
            return response()->json([
                'file_name' => $fileName,
                'download_path' => $path
            ], 200);
        } else {
            return response()->json([
                'message'=>"Sorry, File does not exist in our server or may have been deleted!"
            ], 404);
        }
    }


    public function send(Request $request): JsonResponse
    {
        // default variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;
        $chatMessenger = new ChatifyMessenger();
        /** @var User $auth_user */
        $auth_user = Auth::user();
        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = ChatifyMessenger::getAllowedImages();
            $allowed_files  = ChatifyMessenger::getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            // check file size
            if ($file->getSize() < ChatifyMessenger::getMaxUploadSize()) {
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
            ChatifyMessenger::newMessage([
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
            NotificationService::pushNotification(User::query()->find($request['id']), [
                'title' => trans('Новое сообщение'), 'body' => trans('У вас новое сообщение от user', ['user' => Auth::user()->name])
            ], 'chat', $messageData ?? []);

            return Response::json([
                'success' => true,
                'data' => $messageData ?? [],
                'message' => 'Success',
            ]);
        } else {
            return Response::json([
                'success' => false,
                'data' => $error['message'],
                'message' => 'Fail',
            ]);
        }
    }

    /**
     * fetch [user/group] messages from database
     *
     * @param Request $request
     * @return JsonResponse response
     */
    public function fetch(Request $request): JsonResponse
    {
        $query = ChatifyMessenger::fetchMessagesQuery($request['id']);
        $messages = $query->latest()->get();
        $query->where('seen',0)->update(['seen' => 1]);

        $messages = MessageResource::collection($messages);
        return Response::json([
            'success' => true,
            'data' => $messages,
            'message' => 'Success'
        ]);
    }

    public function seen(Request $request)
    {
        // make as seen
        $seen = Chatify::makeSeen($request['id']);
        // send the response
        return Response::json([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    public function getContacts(): JsonResponse
    {
        $userIdsList = ContactService::contactsList(Auth::user());

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


    public function search(Request $request): JsonResponse
    {
        $input = trim(filter_var($request['name'], FILTER_SANITIZE_STRING));
        $ids = ContactService::contactsList(Auth::user());
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


    public function sharedPhotos(Request $request)
    {
        $images = Chatify::getSharedPhotos($request['user_id']);

        foreach ($images as $image) {
            $image = asset('storage/attachments/' . $image);
        }
        // send the response
        return Response::json([
            'shared' => $images ?? [],
        ]);
    }

    /**
     * Delete conversation
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteConversation(Request $request)
    {
        // delete
        $delete = Chatify::deleteConversation($request['id']);

        // send the response
        return Response::json([
            'deleted' => $delete ? 1 : 0,
        ], 200);
    }

    public function updateSettings(Request $request)
    {
        $msg = null;
        $error = $success = 0;

        // dark mode
        if ($request['dark_mode']) {
            $request['dark_mode'] == "dark"
                ? User::where('id', Auth::id())->update(['dark_mode' => 1])  // Make Dark
                : User::where('id', Auth::id())->update(['dark_mode' => 0]); // Make Light
        }

        // If messenger color selected
        if ($request['messengerColor']) {
            $messenger_color = trim(filter_var($request['messengerColor'], FILTER_SANITIZE_STRING));
            User::where('id', Auth::id())
                ->update(['messenger_color' => $messenger_color]);
        }
        // if there is a [file]
        if ($request->hasFile('avatar')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();

            $file = $request->file('avatar');
            // check file size
            if ($file->getSize() < Chatify::getMaxUploadSize()) {
                if (in_array($file->getClientOriginalExtension(), $allowed_images)) {
                    // delete the older one
                    if (Auth::user()->avatar != config('chatify.user_avatar.default')) {
                        $path = storage_path('app/public/' . config('chatify.user_avatar.folder') . '/' . Auth::user()->avatar);
                        if (file_exists($path)) {
                            @unlink($path);
                        }
                    }
                    // upload
                    $avatar = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $update = User::where('id', Auth::id())->update(['avatar' => $avatar]);
                    $file->storeAs("public/" . config('chatify.user_avatar.folder'), $avatar);
                    $success = $update ? 1 : 0;
                } else {
                    $msg = "File extension not allowed!";
                    $error = 1;
                }
            } else {
                $msg = "File size you are trying to upload is too large!";
                $error = 1;
            }
        }

        // send the response
        return Response::json([
            'status' => $success ? 1 : 0,
            'error' => $error ? 1 : 0,
            'message' => $error ? $msg : 0,
        ]);
    }

    /**
     * Set user's active status
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setActiveStatus(Request $request)
    {
        $update = $request['status'] > 0
            ? User::query()->where('id', $request['user_id'])->update(['active_status' => 1])
            : User::query()->where('id', $request['user_id'])->update(['active_status' => 0]);
        // send the response
        return Response::json([
            'status' => $update,
        ]);
    }
}
