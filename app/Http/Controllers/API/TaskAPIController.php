<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ComplianceTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\User;
use App\Models\Task;
use App\Models\Compliance;
use App\Services\Response;
use App\Models\TaskResponse;
use App\Models\ComplianceType;
use App\Services\TelegramService;
use App\Services\Task\CreateService;
use App\Services\Task\ResponseService;
use App\Services\Task\CreateTaskService;
use App\Services\Task\FilterTaskService;
use App\Http\Resources\SameTaskResource;
use App\Services\Task\UpdateTaskService;
use App\Http\Resources\TaskIndexResource;
use App\Http\Requests\Api\TaskDateRequest;
use App\Http\Requests\Api\TaskNameRequest;
use App\Http\Requests\Api\TaskNoteRequest;
use App\Http\Resources\TaskSingleResource;
use App\Http\Resources\TaskResponseResource;
use App\Http\Requests\Api\TaskRemoteRequest;
use App\Http\Requests\Api\TaskBudgetRequest;
use App\Http\Requests\Api\TaskCustomRequest;
use App\Http\Requests\Api\TaskAddressRequest;
use App\Http\Resources\TaskPaginationResource;
use App\Http\Requests\Api\TaskContactsRequest;
use App\Http\Requests\Api\TaskComplaintRequest;
use App\Http\Requests\Api\TaskVerificationRequest;

class TaskAPIController extends Controller
{
    use Response;

    private CreateService $service;
    private ResponseService $response_service;
    private FilterTaskService $filter_service;
    private CreateTaskService $create_task_service;
    private UpdateTaskService $update_task_service;
    public const SOME_TASK_LIMIT = 10;
    public function __construct()
    {
        $this->service = new CreateService();
        $this->filter_service = new FilterTaskService();
        $this->response_service = new ResponseService();
        $this->create_task_service = new CreateTaskService();
        $this->update_task_service = new UpdateTaskService();
    }

    /**
     * @OA\Get(
     *     path="/api/same-tasks/{task}",
     *     tags={"Task"},
     *     summary="Same tasks by Task ID",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
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
     *     )
     * )
     */
    public function same_tasks(Task $task): AnonymousResourceCollection
    {
        $tasks = $task->category->tasks()->where('id', '!=', $task->id);
        $tasks = $tasks->where('status', Task::STATUS_OPEN)->take(self::SOME_TASK_LIMIT)->orderByDesc('created_at')->get();
        return SameTaskResource::collection($tasks);
    }

    /**
     * @OA\Get(
     *     path="/api/responses/{task}",
     *     tags={"Task"},
     *     summary="Response tasks",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
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
     *     )
     * )
     */
    public function responses(Request $request, Task $task): AnonymousResourceCollection
    {
        if ($task->user_id === auth()->id()) {
            switch ($request->get('filter')){
                case 'rating' :
                    $responses = TaskResponse::query()->select('task_responses.*')->join('users', 'task_responses.performer_id', '=', 'users.id')
                        ->where('task_responses.task_id', '=', $task->id)->orderByDesc('users.review_rating');
                    break;
                case 'date' :
                    $responses = $task->responses()->orderByDesc('created_at');
                    break;
                case 'price' :
                    $responses = $task->responses()->orderBy('price');
                    break;
                default :
                    $responses = $task->responses();
                    break;
            }
        } else {
            $responses = $task->responses()->where('performer_id', auth()->id());
        }
        $responses->where('performer_id', '!=', $task->performer_id);
        return TaskResponseResource::collection($responses->paginate(5));

    }

    /**
     * @OA\Post(
     *     path="/api/task/{task}/response",
     *     tags={"Responses"},
     *     summary="Send Response",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="description",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="price",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="notificate",
     *                    description="0 - xabar kelmasin, 1 - xabar kelsin",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="not_free",
     *                    description="0 - bepul, 1 - pullik",
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
    public function response_store(Task $task, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        switch (true){
            case ((int)$task->user_id === $user->id) :
                return $this->fail(null, trans('trans.your task'));
            case ((int)$user->role_id !== User::ROLE_PERFORMER) :
                return $this->fail(1, trans('trans.not performer')); // 1 -> for open become performer page in app
            case (!$user->is_phone_number_verified) :
                return $this->fail(null, trans('trans.verify phone'));
        }

        $response = $this->response_service->store($request, $task);

        return response()->json($response);
    }

    public function selectPerformer(TaskResponse $response): JsonResponse
    {
        if (!$response->task) {
            return response()->json([
                'success' => false,
                'message' => __('Задача не найдена')
            ]);
        }
        $this->response_service->selectPerformer($response);
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/tasks-filter",
     *     tags={"Task"},
     *     summary="Task filter",
     *     @OA\Parameter (
     *          in="query",
     *          name="categories",
     *          @OA\Schema (
     *              type="array",
     *              @OA\Items (
     *                  type="integer",
     *              )
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="lat",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="long",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="budget",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="is_remote",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="without_response",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="difference",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *
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
     * )
     */
    public function filter(Request $request): AnonymousResourceCollection
    {
        $tasks = $this->filter_service->filter($request->all());

        return TaskSingleResource::collection($tasks);
    }

    public function task_map(Task $task)
    {
        return $task->addresses;
    }


    /**
     * @OA\Get(
     *     path="/api/task/{task}",
     *     tags={"Task"},
     *     summary="Get Task By ID",
     *     @OA\Parameter(
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
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
     */
    public function task(Task $task): TaskIndexResource
    {
        if (auth()->guard('api')->check()) {
            $user_id = auth()->guard('api')->id();
            $viewed_tasks = Cache::get('user_viewed_tasks'. $user_id) ?? [];
            if (!in_array($task->id, $viewed_tasks)) {
                $viewed_tasks[] = $task->id;
            }
            Cache::put('user_viewed_tasks'. $user_id, $viewed_tasks);
            $task->increment('views');
        }
        return new TaskIndexResource($task);
    }

    /**
     * @OA\Get(
     *     path="/api/my-tasks-count",
     *     tags={"Task"},
     *     summary="Get My Tasks Count",
     *     @OA\Parameter(
     *          in="query",
     *          name="is_performer",
     *          required=false,
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
     *     ),
     *     security={
     *      {"token": {}},
     *     },
     * )
     */
    public function my_tasks_count(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $is_performer = $request->get('is_performer');

        $column = $is_performer ? 'performer_id' : 'user_id';

        $open_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_OPEN)->count(), 'status' => Task::STATUS_OPEN];
        $in_process_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_IN_PROGRESS)->count(), 'status' => Task::STATUS_IN_PROGRESS];
        $complete_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_COMPLETE)->count(), 'status' => Task::STATUS_COMPLETE];
        $cancelled_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_CANCELLED)->count(), 'status' => Task::STATUS_CANCELLED];
        $without_reviews = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_NOT_COMPLETED)->count(), 'status' => Task::STATUS_NOT_COMPLETED];
        $all = ['count' => $open_tasks['count'] + $in_process_tasks['count'] + $complete_tasks['count'] + $cancelled_tasks['count'] + $without_reviews['count'], 'status' => 0];


        return response()->json(['success' => true, 'data' => compact('open_tasks', 'in_process_tasks', 'complete_tasks', 'cancelled_tasks', 'without_reviews', 'all')]);
    }

    /**
     * @OA\Get(
     *     path="/api/my-tasks",
     *     tags={"Task"},
     *     summary="Get My Tasks",
     *     @OA\Parameter(
     *          in="query",
     *          name="is_performer",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="status",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
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
     *     ),
     *     security={
     *      {"token": {}},
     *     },
     * )
     */
    public function my_tasks_all(Request $request): TaskPaginationResource
    {
        $request->validate([
            'status' => 'in:0,1,3,4,5,6'
        ]);
        /** @var User $user */
        $user = auth()->user();
        $is_performer = $request->get('is_performer');
        $status = $request->get('status');

        $column = $is_performer ? 'performer_id' : 'user_id';
        $tasks = Task::query()->where($column, $user->id);

        if ($status)
            $tasks = $tasks->where('status', $status);
        else
            $tasks = $tasks->where('status', '!=', 0);

        return new TaskPaginationResource($tasks->orderByDesc('created_at')->paginate());
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/name",
     *     tags={"Task Create"},
     *     summary="Task create name",
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
     *                    property="category_id",
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
    public function name(TaskNameRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->name_store($request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/custom",
     *     tags={"Task Create"},
     *     summary="Task create custom",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
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
    public function custom(TaskCustomRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->custom_store($request->validated(), $request));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/remote",
     *     tags={"Task Create"},
     *     summary="Task create remote",
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
     *                    property="radio",
     *                    description="Agar udallonna bolsa - remote, manzil bo`yicha bo`lsa - address deb yozing",
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
    public function remote(TaskRemoteRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->remote_store($request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/address",
     *     tags={"Task Create"},
     *     summary="Task create address",
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
     *                    property="location",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="latitude",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    property="longitude",
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
    public function address(TaskAddressRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->address_store($request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/date",
     *     tags={"Task Create"},
     *     summary="Task create date",
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
     *                    property="date_type",
     *                    description="1 dan 3 gacha bersa bo`ladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="start_date",
     *                    description="2022-06-03 12:00:0 - manashu formatda kiritiladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="end_date",
     *                    description="2022-06-03 12:00:0 - manashu formatda kiritiladi",
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
    public function date(TaskDateRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->date_store($request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/budget",
     *     tags={"Task Create"},
     *     summary="Task create budget",
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
     *                    property="amount",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    property="budget_type",
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
    public function budget(TaskBudgetRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->budget_store($request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/note",
     *     tags={"Task Create"},
     *     summary="Task create note",
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
     *                    property="description",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="docs",
     *                    description="true - 1, false - 0",
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
    public function note(TaskNoteRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->note_store($request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/images",
     *     tags={"Task Create"},
     *     summary="Task create images",
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
     *                    property="images",
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
     * @throws ValidationException
     */
    public function uploadImages(Request $request): JsonResponse
    {
        return $this->create_task_service->image_store($request);
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/contacts",
     *     tags={"Task Create"},
     *     summary="Task create contacts",
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
    public function contacts(TaskContactsRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->contact_store($request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/verify",
     *     tags={"Task Create"},
     *     summary="Task create verify",
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
     *                    property="phone_number",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="sms_otp",
     *                    description="Telefonga kelgan SMS code",
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
    public function verify(TaskVerificationRequest $request): JsonResponse
    {
        return $this->create_task_service->verification($request->validated());
    }


    /**
     * @OA\Post(
     *      path="/api/update-task/{task}/name",
     *      tags={"Task Update"},
     *      summary="Task update name",
     *      @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody (
     *          required=true,
     *          @OA\MediaType (
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property (
     *                      property="name",
     *                      type="string",
     *                  ),
     *                  @OA\Property (
     *                      property="category_id",
     *                      type="integer",
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response (
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response (
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response (
     *          response=403,
     *          description="Forbidden",
     *      ),
     *      security={
     *          {"token": {}}
     *      },
     *
     *
     * )
     *
     *
     */
    public function updateName(TaskNameRequest $request, Task $task): JsonResponse
    {
        return $this->success($this->update_task_service->updateName($task, $request->validated()));
    }

    public function updateCustom(Request $request, Task $task): JsonResponse
    {
        return $this->success($this->update_task_service->updateCustom($task, $request));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/remote",
     *     tags={"Task Update"},
     *     summary="Task update remote",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
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
     *                    property="radio",
     *                    description="Agar udallonna bolsa - remote, manzil bo`yicha bo`lsa - address deb yozing",
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
    public function updateRemote(TaskRemoteRequest $request, Task $task): JsonResponse
    {
        return $this->success($this->update_task_service->updateRemote($task, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/address",
     *     tags={"Task Update"},
     *     summary="Task update address",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
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
     *                    property="location",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="latitude",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    property="longitude",
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
    public function updateAddress(TaskAddressRequest $request, Task $task): JsonResponse
    {
        return $this->success($this->update_task_service->updateAddress($task, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/date",
     *     tags={"Task Update"},
     *     summary="Task update date",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
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
     *                    property="date_type",
     *                    description="1 dan 3 gacha bersa bo`ladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="start_date",
     *                    description="2022-06-03 12:00:0 - manashu formatda kiritiladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="end_date",
     *                    description="2022-06-03 12:00:0 - manashu formatda kiritiladi",
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
    public function updateDate(TaskDateRequest $request, Task $task): JsonResponse
    {
        return $this->success($this->update_task_service->updateDate($task, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/budget",
     *     tags={"Task Update"},
     *     summary="Task update budget",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
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
     *                    property="amount",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    property="budget_type",
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
    public function updateBudget(TaskBudgetRequest $request, Task $task): JsonResponse
    {
        return $this->success($this->update_task_service->updateBudget($task, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/note",
     *     tags={"Task Update"},
     *     summary="Task update note",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
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
     *                    property="description",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="docs",
     *                    description="true - 1, false - 0",
     *                    type="boolean",
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
    public function updateNote(TaskNoteRequest $request, Task $task): JsonResponse
    {
        return $this->success($this->update_task_service->updateNote($task, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/images",
     *     tags={"Task Update"},
     *     summary="Task update images",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
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
     *                    property="images",
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
    public function updateUploadImages(Request $request, Task $task): JsonResponse
    {
        return $this->update_task_service->updateImage($task, $request);
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/contacts",
     *     tags={"Task Update"},
     *     summary="Task update contacts",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
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
    public function updateContacts(TaskContactsRequest $request, Task $task): JsonResponse
    {
        return $this->success($this->update_task_service->updateContact($task, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/verify",
     *     tags={"Task Update"},
     *     summary="Task update verify",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
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
     *                    property="phone_number",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="sms_otp",
     *                    description="Telefonga kelgan SMS code",
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
    public function updateVerify(TaskVerificationRequest $request, Task $task): JsonResponse
    {
        return $this->update_task_service->verification($task, $request->validated());
    }

    public function deleteImage(Request $request, Task $task): JsonResponse
    {
        return $this->update_task_service->deleteImage($request, $task);
    }


    /**
     * @OA\Post(
     *     path="/api/task/{task}/complain",
     *     tags={"Complain"},
     *     summary="Task complain",
     *     @OA\Parameter (
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="compliance_type_id",
     *                    type="integer",
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
    public function complain(TaskComplaintRequest $request, Task $task): JsonResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        $data['task_id'] = $task->id;
        $data['user_id'] = $user->id;
        /** @var Compliance $compliant */
        $compliant = Compliance::query()->create($data);
        $data['id'] = $compliant->id;
        $data['complaint'] = $compliant->text;
        $data['user_name'] = $user->name;
        $data['task_name'] = $task->name;
        if (setting('site.bot_token') && setting('site.channel_username'))
            (new TelegramService())->sendMessage($data);
        return response()->json([
            'success' => true,
            'message' => trans('trans.Complaint is sent.')
        ]);
    }

    public function complainTypes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ComplianceTypeResource::collection(ComplianceType::all())
        ]);
    }
}
