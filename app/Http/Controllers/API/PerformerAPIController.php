<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BecomePerformerEmailPhone;
use App\Http\Requests\BecomePerformerRequest;
use App\Http\Requests\GiveTaskRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PerformerIndexResource;
use App\Http\Resources\PerformerPaginateResource;
use App\Http\Resources\ReviewIndexResource;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PlayMobile\SMS\SmsService;

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
        $performers = User::query()->where('role_id', 2);
        if (isset($request->online))
        {
            $date = Carbon::now()->subMinutes(2)->toDateTimeString();
            $performers = $performers->where('role_id', 2)->where('last_seen', ">=",$date);
        }

        return PerformerIndexResource::collection($performers->paginate($request->per_page));
    }

    public function online_performers()
    {
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        $performers = User::where('role_id', 2)->where('last_seen', ">=",$date)->paginate();
        return new PerformerPaginateResource($performers);
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

        return $performer->role_id == 5 ? new PerformerIndexResource($performer) : abort(404);
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
        $task = Task::where('id', $data['task_id'])->first();
        $performer = User::query()->findOrFail($data['performer_id']);
        $text_url = route("searchTask.task",$data['task_id']);
        $text = "Заказчик предложил вам новую задания $text_url. Имя заказчика: " . $task->user->name;
        (new SmsService())->send($performer->phone_number, $text);
        $notification = Notification::create([
            'user_id' => $task->user_id,
            'performer_id' => $data['performer_id'],
            'task_id' => $data['task_id'],
            'name_task' => $task->name,
            'description' => '123',
            'type' => Notification::GIVE_TASK,
        ]);

        NotificationService::sendNotificationRequest([$data['performer_id']], [
            'url' => 'detailed-tasks' . '/' . $data['task_id'], 'name' => $task->name, 'time' => 'recently'
        ]);
        NotificationService::pushNotification($performer->firebase_token, [
            'title' => __('Предложение'), 'body' => __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
            ])
        ], 'notification', new NotificationResource($notification));

        return response()->json(['success' => true, 'message' => 'Success']);
    }

    public function validatorRules($step)
    {
        if ($step == 1) {
            return [
                'name' => 'required',
                'address' => 'required',
                'birth_date' => 'required'
            ];
        } elseif ($step == 2) {
            return [
                'phone_number' => 'required',
                'email' => 'required|email'
            ];
        } elseif ($step == 3) {
            return [
                'avatar' => 'required'
            ];
        } else {
            return [
                'categories' => 'required'
            ];
        }
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
        $user = auth()->user();
        $user->update($data);

        return response()->json(['success' => 'true', 'message' => 'Successfully updated']);
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
        $request->validated();
        return response()->json(['success' => 'true', 'message' => 'Successfully updated']);
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

        return response()->json(['success' => true, "message" => 'successfully updated']);

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
            ->where('user_id',auth()->user()->id)
            ->fromUserType($request->get('from'))
            ->type($request->get('type'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => ReviewIndexResource::collection($reviews),
            'message' => 'Success'
        ]);
    }

    public function getByCategories()
    {
        return response()->json(['id' => request()->category_id]);
    }
}
