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
            NotificationResource::collection(NotificationService::getNotifications(auth()->user(), false))
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

    public function show_notification(Notification $notification)
    {
        $notification->update(['is_read' => 1]);
        if ((int)$notification->type === Notification::NEWS_NOTIFICATION || (int)$notification->type === Notification::SYSTEM_NOTIFICATION) {
            return redirect('/news/' . $notification->id);
        }
        if ((int)$notification->type === Notification::NEW_PASSWORD ){
            return redirect('/profile/settings');
        }
        if ((int)$notification->type === Notification::WALLET_BALANCE ){
            return redirect('/profile/cash');
        }
        return redirect('/detailed-tasks/' . $notification->task_id);
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

            NotificationService::sendNotification($data, $slug);

            return $redirect->with([
                'message' => __('voyager::generic.successfully_added_new') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
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
     *     path="/api/test-firebase-notification",
     *     tags={"Test Notifications"},
     *     summary="test firebase notification",
     *     @OA\RequestBody (
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="user_id",
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
    public function test_firebase_notification(Request $request): JsonResponse
    {
        $data = $request->get('user_id');
        if ($data === 'null'){
            $performers = User::query()->where('role_id', User::ROLE_PERFORMER)->select('id', 'email', 'firebase_token', 'sms_notification', 'email_notification', 'phone_number')->get();
            foreach ($performers as $performer) {
                /** @var Notification $notification */
                $notification = Notification::query()->create([
                    'performer_id' => $performer->id,
                    'description' => 'test notif',
                    "type" => 15
                ]);
                NotificationService::pushNotification($performer, [
                    'title' => 'test firebase notification title',
                    'body' => 'test firebase notification body'
                ], 'notification', new NotificationResource($notification));
            }
        }else{
            $notification = Notification::query()->create([
                'user_id'=> $data,
                'description' => '123',
                'type' => 15,
            ]);
            $performer = User::query()->findOrFail($data);
            NotificationService::pushNotification($performer, [
                'title' => 'test firebase notification title',
                'body' => 'test firebase notification body'
            ], 'notification', new NotificationResource($notification));
        }

        return response()->json(['success' => true, 'message' => 'success']);
    }

}
