<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BecomePerformerEmailPhone;
use App\Http\Requests\BecomePerformerRequest;
use App\Http\Requests\GiveTaskRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PerformerIndexResource;
use App\Http\Resources\ReviewIndexResource;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SmsMobileService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerformerAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/performers",
     *     tags={"Performers"},
     *     summary="Get All Performers",
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
     *     )
     * )
     *
     */
    public function service(Request $request)
    {
        $performers = User::query()
            ->where('role_id', 2)
            ->withoutBlockedPerformers(auth()->id())
            ->orderByDesc('review_rating')
            ->orderByRaw('(review_good - review_bad) DESC');
        if (isset($request->online))
        {
            $date = Carbon::now()->subMinutes(2)->toDateTimeString();
            $performers = $performers->where('role_id', 2)->where('last_seen', ">=",$date);
        }
        return PerformerIndexResource::collection($performers->paginate($request->get('per_page')));
    }

    /**
     * @OA\Get(
     *     path="/api/performers/{performer}",
     *     tags={"Performers"},
     *     summary="Get Performer By ID",
     *     @OA\Parameter(
     *          in="path",
     *          name="performer",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
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
     *     )
     * )
     *
     */
    public function performer(User $performer)
    {
        setView($performer);

        return (int)$performer->role_id === 5 ? new PerformerIndexResource($performer) : abort(404);
    }

    /**
     * @OA\Post(
     *     path="/api/give-task",
     *     tags={"Task"},
     *     summary="Give task by task ID and perfomer ID",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="performer_id",
     *                    type="integer",
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
    public function give_task(GiveTaskRequest $request)
    {
        $data = $request->validated();
        /** @var Task $task */
        $task = Task::query()->where('id', $data['task_id'])->first();
        /** @var User $performer */
        $performer = User::query()->findOrFail($data['performer_id']);
        $locale = cacheLang($performer->id);
        $text_url = route("searchTask.task",$data['task_id']);
        $message = __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
            'task_name' => $text_url, 'task_id' => $task->id, 'task_user' => $task->user?->name
        ], $locale);
        $phone_number = $performer->phone_number;
        SmsMobileService::sms_packages(correctPhoneNumber($phone_number), $message);
        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'performer_id' => $data['performer_id'],
            'task_id' => $data['task_id'],
            'name_task' => $task->name,
            'description' => '123',
            'type' => Notification::GIVE_TASK,
        ]);

        NotificationService::sendNotificationRequest([$data['performer_id']], [
            'created_date' => $notification->created_at->format('d M'),
            'title' => NotificationService::titles($notification->type),
            'url' => route('show_notification', [$notification]),
            'description' => NotificationService::descriptions($notification)
        ]);
//        NotificationService::sendNotificationRequest([$data['performer_id']], [
//            'url' => 'detailed-tasks' . '/' . $data['task_id'], 'name' => $task->name, 'time' => 'recently'
//        ]);
        NotificationService::pushNotification($performer, [
            'title' => NotificationService::titles($notification->type, $locale),
            'body' => NotificationService::descriptions($notification, $locale)
        ], 'notification', new NotificationResource($notification));

        return response()->json(['success' => true, 'message' => 'Success']);
    }

    /**
     * @OA\Post(
     *     path="/api/become-performer",
     *     tags={"Become a perfomer"},
     *     summary="Initial Data",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="name",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="location",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="born_date",
     *                    type="number",
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
    public function becomePerformerData(BecomePerformerRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        $data['born_date'] = Carbon::parse($data['born_date'])->format('Y-m-d');
        $user->update($data);

        return response()->json(['success' => 'true', 'message' => __('Успешно обновлено')]);
    }

    /**
     * @OA\Post(
     *     path="/api/become-performer-phone",
     *     tags={"Become a perfomer"},
     *     summary="Email and phone",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="email",
     *                    type="string",
     *                    format="email",
     *                 ),
     *                 @OA\Property (
     *                    property="phone_number",
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
    public function becomePerformerEmailPhone(BecomePerformerEmailPhone $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        if ($data['phone_number'] !== $user->phone_number) {
            $user->phone_number = $data['phone_number'];
            $user->is_phone_number_verified = 0;
        }
        if ($data['email'] !== $user->email) {
            $user->email = $data['email'];
            $user->is_email_verified = 0;
        }
        $user->save();
        return response()->json(['success' => 'true', 'message' => __('Успешно обновлено')]);
    }

    /**
     * @OA\Post(
     *     path="/api/become-performer-avatar",
     *     tags={"Become a perfomer"},
     *     summary="Avator",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="avatar",
     *                    type="file",
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
    public function becomePerformerAvatar(Request $request)
    {
        $data = $request->validate(['avatar'=>'required']);
        $avatar = $data['avatar'];

        $data['role_id'] = 2;
        $name = Storage::put('public/uploads', $avatar);
        $name = str_replace('public/', '', $name);
        $data['avatar'] = $name;
        auth()->user()->update($data);

        return response()->json(['success' => true, 'message' => 'true']);


    }

    /**
     * @OA\Post(
     *     path="/api/become-performer-category",
     *     tags={"Become a perfomer"},
     *     summary="Categories",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="category_id",
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
    public function becomePerformerCategory(Request $request)
    {
        $data = $request->validate(['category_id' => 'required|string']);

        auth()->user()->update($data);

        return response()->json(['success' => true, "message" => __('Успешно обновлено')]);

    }


    /**
     * @OA\Get(
     *     path="/api/reviews",
     *     tags={"Performers"},
     *     summary="Performer reviews",
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
    public function reviews(Request $request)
    {
        $reviews = Review::query()
            ->whereHas('task')->whereHas('user')
            ->where('user_id',auth()->id())
            ->fromUserType($request->get('from'))
            ->type($request->get('type'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => ReviewIndexResource::collection($reviews),
            'message' => 'Success'
        ]);
    }

}
