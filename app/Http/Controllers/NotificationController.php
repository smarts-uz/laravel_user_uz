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
    public function getNotifications()
    {
        return $this->success(
            NotificationResource::collection(NotificationService::getNotifications(auth()->user(), false))
        );
    }

    public function read_notification(Notification $notification): JsonResponse
    {
        $notification->update(['is_read' => 1]);
        return $this->success($notification);
    }

    public function show_notification(Notification $notification)
    {
        $notification->update(['is_read' => 1]);
        if ((int)$notification->type === Notification::NEWS_NOTIFICATION || (int)$notification->type === Notification::SYSTEM_NOTIFICATION) {
            return redirect('/news');
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
}
