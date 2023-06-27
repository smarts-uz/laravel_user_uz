<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\FirebaseTokenRequest;
use App\Jobs\SendNewsNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Redirector;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use App\Models\{Notification, Session, User};
use App\Services\{NotificationService, Response};
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\Support\Str;
use TCG\Voyager\{Events\BreadDataAdded, Facades\Voyager, Http\Controllers\VoyagerBaseController};

class NotificationController extends VoyagerBaseController
{
    use Response;
    protected NotificationService $notificationService;


    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    /**
     * @OA\Get(
     *     path="/api/notifications",
     *     tags={"Notifications"},
     *     summary="Get notifications",
     *     description="[**Telegram :** https://t.me/c/1334612640/189](https://t.me/c/1334612640/189).",
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
    public function getNotifications(): JsonResponse
    {
        $user = auth()->user();
        $data = $this->notificationService->getNotifService($user);
        return $this->success($data);
    }

    /**
     * @OA\Post(
     *     path="/api/read-notification/{notificationId}",
     *     tags={"Notifications"},
     *     summary="Read notifications",
     *     description="[**Telegram :** https://t.me/c/1334612640/201](https://t.me/c/1334612640/201).",
     *     @OA\Parameter (
     *          in="path",
     *          description="Bildirishnoma idsi kiritiladi",
     *          name="notificationId",
     *          required=true,
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
    public function read_notification($notificationId): JsonResponse
    {
        $notification = Notification::find($notificationId);
        $notification->update(['is_read' => 1]);
        return $this->success($notification);
    }

    /**
     * @OA\Post(
     *     path="/api/read-all-notification",
     *     tags={"Notifications"},
     *     summary="Read all notifications",
     *     description="[**Telegram :** https://t.me/c/1334612640/249](https://t.me/c/1334612640/249).",
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
    public function read_all_mobile_notification(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        NotificationService::readAllNotifications($user->id);
        return response()->json([
            'success' => true,
            'message' => __('success'),
            'data' => $user->id
        ]);
    }

    public function show_notification(Notification $notification): Redirector|Application|RedirectResponse
    {
        $notification->update(['is_read' => 1]);
        return match ($notification->type) {
            Notification::NEWS_NOTIFICATION => redirect('/news/' . $notification->news_id),
            Notification::NEW_PASSWORD => redirect('/profile/settings'),
            Notification::WALLET_BALANCE => redirect('/profile/cash'),
            Notification::TEST_PUSHER_NOTIFICATION => redirect('/'),
            default => redirect('/detailed-tasks/' . $notification->task_id),
        };
    }

    public function read_all_notification($user_id): JsonResponse
    {
        NotificationService::readAllNotifications($user_id);
        return $this->success([
            "user_id" => $user_id,
        ]);
    }


    public function show_notification_user(Notification $notification): Redirector|Application|RedirectResponse
    {
        $notification->update(['is_read' => 1]);
        return redirect('/performers/' . $notification->user_id);
    }


    /**
     * @OA\Post(
     *     path="/api/firebase-token",
     *     tags={"Notifications"},
     *     summary="Firebase token",
     *     description="[**Telegram :** https://t.me/c/1334612640/199](https://t.me/c/1334612640/199).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="token",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="device_id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="device_name",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="platform",
     *                    type="string",
     *                 ),
     *             ),
     *         ),
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
    public function setToken(FirebaseTokenRequest $request): JsonResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        $session = Session::query()->updateOrCreate(
            ['device_id' => $data['device_id']],
            [
                'id' => Str::random(40),
                'firebase_token' => $data['token'],
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'payload' => $data['token'],
                'last_activity' => now()->timestamp,
                'device_name' => $data['device_name'],
                'platform' => $data['platform'],
                'is_mobile' => 1
            ]
        );

        if (setting('admin.bonus',0) > 0) {
           $push_notif = Notification::query()
                ->where('user_id',$user->id)
                ->where('type',Notification::WALLET_BALANCE)
                ->where('status',1)
                ->exists();
            if (!$push_notif) {
                /** @var Notification $notification */
                $notification = Notification::query()->create([
                    'user_id' => $user->id,
                    'description' => 'wallet',
                    'type' => Notification::WALLET_BALANCE,
                ]);
                NotificationService::pushNoti($user, $notification);
            }
        }

        if (!($user->password)){
            $push_notif = Notification::query()
                ->where('user_id',$user->id)
                ->where('type',Notification::NEW_PASSWORD)
                ->where('status',1)
                ->exists();
            if(!$push_notif){
                /** @var Notification $notification */
                $notification = Notification::query()->create([
                    'user_id' => $user->id,
                    'description' => 'password',
                    'type' => Notification::NEW_PASSWORD,
                ]);
                NotificationService::pushNoti($user, $notification);
            }
        }

        return $this->success($session);
    }

    /**
     * @throws AuthorizationException
     * @throws JsonException
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());
        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            // dispatch queue job
            dispatch(new SendNewsNotification($data));

            return $redirect->with([
                'message' => __('voyager::generic.successfully_added_new') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/count/notifications",
     *     tags={"Notifications"},
     *     summary="Notifications count",
     *     description="[**Telegram :** https://t.me/c/1334612640/200](https://t.me/c/1334612640/200).",
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
    public function count()
    {
        $count = NotificationService::getNotifications(auth()->user())->count();
        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/firebase-notification",
     *     tags={"Notifications"},
     *     summary="firebase notification",
     *     description="[**Telegram :** https://t.me/c/1334612640/235](https://t.me/c/1334612640/235).",
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    description="push notif jo'natiladigan user id, bunga qiymat kiritilsa role tanlanmasiligi kerak",
     *                    property="user_id",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="push notif jo'natiladigan role, agar role tanlansa user id kiritilmaydi",
     *                    property="type",
     *                    type="string",
     *                    default="default",
     *                    enum={"all", "role_user","role_performer","role_admin"}
     *                 ),
     *                 @OA\Property (
     *                    description="push notif title",
     *                    property="title",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="push notif text",
     *                    property="text",
     *                    type="string",
     *                 ),
     *             ),
     *         ),
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
    public function firebase_notification(Request $request): array
    {

        $user_id = $request->get('user_id');
        $type = $request->get('type');
        $title = $request->get('title');
        $text = $request->get('text');

        return $this->notificationService->firebase_notif($type,$title,$text,$user_id);
    }

    /**
     * @OA\Post(
     *     path="/api/pusher-notification",
     *     tags={"Notifications"},
     *     summary="pusher notification",
     *     description="[**Telegram :** https://t.me/c/1334612640/236](https://t.me/c/1334612640/236).",
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    description="pusher orqali notif jo'natiladigan user id, bunga qiymat kiritilsa role tanlanmasiligi kerak",
     *                    property="user_id",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="pusher orqali notif jo'natiladigan role, agar role tanlansa user id kiritilmaydi",
     *                    property="type",
     *                    type="string",
     *                    default="",
     *                    enum={"all", "role_user","role_performer","role_admin"}
     *                 ),
     *                 @OA\Property (
     *                    description="pusher notif title",
     *                    property="title",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="pusher notif title",
     *                    property="text",
     *                    type="string",
     *                 ),
     *             ),
     *         ),
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
    public function pusher_notification(Request $request): array
    {

        $user_id = $request->get('user_id');
        $type = $request->get('type');
        $title = $request->get('title');
        $text = $request->get('text');

        return $this->notificationService->pusher_notif($type, $title, $text, $user_id);
    }


    /**
     * @OA\Post(
     *     path="/api/sms-notification",
     *     tags={"Notifications"},
     *     summary="sms notification",
     *     description="[**Telegram :** https://t.me/c/1334612640/237](https://t.me/c/1334612640/237).",
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    description="sms notif jo'natiladigan user id, bunga qiymat kiritilsa role tanlanmasiligi kerak",
     *                    property="user_id",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="sms notif jo'natiladigan role, agar role tanlansa user id kiritilmaydi",
     *                    property="type",
     *                    type="string",
     *                    default="",
     *                    enum={"all", "role_user","role_performer","role_admin"}
     *                 ),
     *                 @OA\Property (
     *                    description="sms notif text",
     *                    property="text",
     *                    type="string",
     *                 ),
     *             ),
     *         ),
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
    public function sms_notification(Request $request): array
    {
        $user_id = $request->get('user_id');
        $type = $request->get('type');
        $text = $request->get('text');

        return $this->notificationService->sms_notif($type, $text, $user_id);
    }


    /**
     * @OA\Post(
     *     path="/api/email-notification",
     *     tags={"Notifications"},
     *     summary="email notification",
     *     description="[**Telegram :** https://t.me/c/1334612640/238](https://t.me/c/1334612640/238).",
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    description="email notif jo'natiladigan user id, bunga qiymat kiritilsa role tanlanmasiligi kerak",
     *                    property="user_id",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="email notif jo'natiladigan role, agar role tanlansa user id kiritilmaydi",
     *                    property="type",
     *                    type="string",
     *                    default="",
     *                    enum={"all", "role_user","role_performer","role_admin"}
     *                 ),
     *                 @OA\Property (
     *                    description="email notif text",
     *                    property="text",
     *                    type="string",
     *                 ),
     *             ),
     *         ),
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
    public function email_notification(Request $request): array
    {
        $user_id = $request->get('user_id');
        $type = $request->get('type');
        $text = $request->get('text');

        return $this->notificationService->email_notif($type, $text, $user_id);
    }


    /**
     * @OA\Post(
     *     path="/api/task-create-notification",
     *     tags={"Notifications"},
     *     summary="task create notification",
     *     description="[**Telegram :** https://t.me/c/1334612640/250](https://t.me/c/1334612640/250).",
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    description="create task id",
     *                    property="id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    description="task name",
     *                    property="name",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="task category id, masalan 31",
     *                    property="category_id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    description="task create qilganda push notif uchun title",
     *                    property="title",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="task create qilganda push notif uchun body",
     *                    property="body",
     *                    type="string",
     *                 ),
     *             ),
     *         ),
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
    #[ArrayShape(['success' => "bool", 'message' => "string", 'data' => "array"])]
    public function task_create_notification(Request $request): array
    {
        $user_id = auth()->id();
        $task_id = $request->get('id');
        $task_name = $request->get('name');
        $task_category_id = $request->get('category_id');
        $title = $request->get('title');
        $body = $request->get('body');
        return $this->notificationService->task_create_notification($user_id, $task_id, $task_name, $task_category_id, $title, $body);
    }


}
