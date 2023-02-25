<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\FirebaseTokenRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\Session;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

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
        return $this->success(
            NotificationResource::collection(NotificationService::getNotifications(auth()->user()))
        );
    }

    /**
     * @OA\Post(
     *     path="/api/read-notification/{notification}",
     *     tags={"Notifications"},
     *     summary="Read notifications",
     *     @OA\Parameter (
     *          in="path",
     *          name="notification",
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
    public function read_notification(Notification $notification): JsonResponse
    {
        $notification->update(['is_read' => 1]);
        return $this->success($notification);
    }

    /**
     * @OA\Post(
     *     path="/api/read-all-notification",
     *     tags={"Notifications"},
     *     summary="Read all notifications",
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
        ]);
    }

    public function show_notification(Notification $notification)
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


    public function show_notification_user(Notification $notification)
    {
        $notification->update(['is_read' => 1]);
        return redirect('/performers/' . $notification->user_id);
    }


    /**
     * @OA\Post(
     *     path="/api/firebase-token",
     *     tags={"Profile"},
     *     summary="Firebase token",
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

        if (setting('admin.bonus') > 0) {
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

            NotificationService::sendNotification($data);

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
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="user_id",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="type",
     *                    type="string",
     *                    default="default",
     *                    enum={"all", "role_user","role_performer","role_admin"}
     *                 ),
     *                 @OA\Property (
     *                    property="title",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
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
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="user_id",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="type",
     *                    type="string",
     *                    default="",
     *                    enum={"all", "role_user","role_performer","role_admin"}
     *                 ),
     *                 @OA\Property (
     *                    property="title",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
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
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="user_id",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="type",
     *                    type="string",
     *                    default="",
     *                    enum={"all", "role_user","role_performer","role_admin"}
     *                 ),
     *                 @OA\Property (
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
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="user_id",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="type",
     *                    type="string",
     *                    default="",
     *                    enum={"all", "role_user","role_performer","role_admin"}
     *                 ),
     *                 @OA\Property (
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
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="name",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="category_id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="title",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
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
    public function task_create_notification(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $task_id = $request->get('id');
        $task_name = $request->get('name');
        $task_category_id = $request->get('category_id');
        $title = $request->get('title');
        $body = $request->get('body');
        return $this->notificationService->task_create_notification($user, $task_id, $task_name, $task_category_id, $title, $body);
    }


}
