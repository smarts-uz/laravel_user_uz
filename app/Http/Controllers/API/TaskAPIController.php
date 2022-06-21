<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Api\TaskAddressRequest;
use App\Http\Requests\Api\TaskBudgetRequest;
use App\Http\Requests\Api\TaskContactsRequest;
use App\Http\Requests\Api\TaskCustomRequest;
use App\Http\Requests\Api\TaskDateRequest;
use App\Http\Requests\Api\TaskFilterRequest;
use App\Http\Requests\Api\TaskNameRequest;
use App\Http\Requests\Api\TaskNoteRequest;
use App\Http\Requests\Api\TaskRemoteRequest;
use App\Http\Requests\Api\TaskVerificationRequest;
use App\Http\Requests\Api\V1\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Requests\Api\TaskComplaintRequest;
use App\Http\Resources\SameTaskResource;
use App\Http\Resources\TaskIndexResource;
use App\Http\Resources\TaskPaginationResource;
use App\Http\Resources\TaskResponseResource;
use App\Http\Resources\TaskSingleResource;
use App\Models\Compliance;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\User;
use App\Services\Response;
use App\Services\Task\CreateService;
use App\Services\Task\CreateTaskService;
use App\Services\Task\FilterTaskService;
use App\Services\Task\ResponseService;
use App\Services\Task\UpdateTaskService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\CustomFieldsValue;

class TaskAPIController extends Controller
{
    use Response;

    private $service;
    private $response_service;
    private $filter_service;
    private $create_task_service;
    private $update_task_service;

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
    public function same_tasks(Task $task, Request $request)
    {
        $tasks = $task->category->tasks()->where('id', '!=', $task->id);
        $tasks = $tasks->where('status', Task::STATUS_OPEN)->take(10)->get();
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
    public function responses(Request $request, Task $task)
    {
        if ($task->user_id == auth()->id()) {
            if ($request->get('filter') == 'rating') {
                $responses = TaskResponse::query()->select('task_responses.*')->join('users', 'task_responses.performer_id', '=', 'users.id')
                    ->where('task_responses.task_id', '=', $task->id)->orderByDesc('users.review_rating');
            } elseif ($request->get('filter') == 'date') {
                $responses = $task->responses()->orderByDesc('created_at');
            } elseif ($request->get('filter') == 'price') {
                $responses = $task->responses()->orderBy('price');
            } else {
                $responses = $task->responses();
            }
        } else {
            $responses = $task->responses()->where('performer_id', auth()->id());
        }
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
    public function response_store(Task $task, Request $request)
    {
        $user = auth()->user();
        if ($task->user_id == $user->id) {
            return $this->fail([], "Bu o'zingizning taskingiz");
        } elseif ($user->role_id != 2) {
            return $this->fail([], "Siz Performer emassiz");
        }

        $response = $this->response_service->store($request, $task);

        return response()->json($response);
    }

    public function selectPerformer(TaskResponse $response)
    {
        if (!$response->task) {
            return response()->json([
                'success' => false,
                'message' => "Task not found"
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
    public function filter(Request $request)
    {
        $tasks = $this->filter_service->filter($request->all());

        return TaskSingleResource::collection($tasks);
    }

    public function task_map(Task $task)
    {
        return $task->addresses;
    }

    public function task_find(Request $request)
    {
        if (isset($request->s)) {
            $s = $request->s;
            $tasks = Task::query()->where('status', Task::STATUS_OPEN)
                ->where('name', 'like', "%$s%")
                ->orWhere('description', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%")
                ->orWhere('budget', 'like', "%$s%")->paginate();
        } else {
            $tasks = Task::query()->where('status', Task::STATUS_OPEN)->paginate();
        }
        return new TaskPaginationResource($tasks);
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
    public function task(Task $task)
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
    public function my_tasks_count(Request $request)
    {
        $user = auth()->user();
        $is_performer = $request->is_performer;

        $column = $is_performer ? 'performer_id' : 'user_id';

        $open_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_OPEN)->count(), 'status' => Task::STATUS_OPEN];
        $in_process_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_IN_PROGRESS)->count(), 'status' => Task::STATUS_IN_PROGRESS];
        $complete_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_COMPLETE)->count(), 'status' => Task::STATUS_COMPLETE];
        $cancelled_tasks = ['count' => Task::withTrashed()->where($column, $user->id)->where('status', Task::STATUS_COMPLETE_WITHOUT_REVIEWS)->count(), 'status' => Task::STATUS_COMPLETE_WITHOUT_REVIEWS];
        $all = ['count' => $open_tasks['count'] + $in_process_tasks['count'] + $complete_tasks['count'] + $cancelled_tasks['count'], 'status' => 0];


        return response()->json(['success' => true, 'data' => compact('open_tasks', 'in_process_tasks', 'complete_tasks', 'cancelled_tasks', 'all')]);
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
    public function my_tasks_all(Request $request)
    {
        $user = auth()->user();
        $is_performer = $request->is_performer;
        $status = $request->status;

        $status = in_array($status, [Task::STATUS_OPEN, Task::STATUS_COMPLETE_WITHOUT_REVIEWS, Task::STATUS_COMPLETE, Task::STATUS_IN_PROGRESS]) ? $status : 0;

        $column = $is_performer ? 'performer_id' : 'user_id';
        $tasks = Task::withTrashed()->where($column, $user->id);

        if ($status)
            $tasks = $tasks->where('status', $status);
        else
            $tasks = $tasks->where('status', '!=', 0);

        return new TaskPaginationResource($tasks->paginate());
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
    public function name(TaskNameRequest $request)
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
    public function custom(TaskCustomRequest $request)
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
    public function remote(TaskRemoteRequest $request)
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
    public function address(TaskAddressRequest $request)
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
    public function date(TaskDateRequest $request)
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
    public function budget(TaskBudgetRequest $request)
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
    public function note(TaskNoteRequest $request)
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
     */
    public function uploadImages(Request $request)
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
    public function contacts(TaskContactsRequest $request)
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
    public function verify(TaskVerificationRequest $request)
    {
        return $this->create_task_service->verification($request->validated());
    }

    /**
     * @OA\Post(
     *     path="/api/task/create",
     *     tags={"Task"},
     *     summary="Task create",
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
     *                    property="address",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="date_type",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="budget",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    property="description",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="category_id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="photos",
     *                    type="file",
     *                 ),
     *                 @OA\Property (
     *                    property="phone",
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
    public function create(StoreRequest $request)
    {
        $data = $request->validated();
        $data["user_id"] = auth()->user()->id;

        $images = isset($data['photos']) ? $data['images'] : [];
        $data['photos'] = [];
        foreach ($images as $image) {
            $name = md5(\Carbon\Carbon::now() . '_' . $image->getClientOriginalName() . '.' . $image->getClientOriginalExtension());
            $filepath = Storage::disk('public')->putFileAs('/images', $image, $name);
            $data['photos'][] = $filepath;
        }


        $result = Task::create($data);
        if ($result)
            return response()->json([
                'message' => 'Created successfuly',
                'success' => true,
                'data' => $result
            ]);
        return response()->json([
            'message' => 'Something wrong',
            'success' => false,
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/change-task/{task}",
     *     tags={"Change Task"},
     *     summary="Get Task",
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
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function getTask(Task $task)
    {
        return $task;
    }

    /**
     * @OA\Put(
     *     path="/api/change-task/{task}",
     *     tags={"Task"},
     *     summary="Change task",
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
     *                    property="name",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="location0",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="coordinates0",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="date_type",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="budget",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    property="description",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="category_id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="photos",
     *                    type="file",
     *                 ),
     *                 @OA\Property (
     *                    property="phone",
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
    public function changeTask(UpdateRequest $request, Task $task)
    {
        taskGuard($task);
        $data = $request->validated();
        //$data = getAddress($data); // шуни комментировать килиб койса swagger да ишлайди

        $images = isset($data['photos']) ? $data['images'] : [];
        $data['photos'] = [];
        foreach ($images as $image) {
            $name = md5(\Carbon\Carbon::now() . '_' . $image->getClientOriginalName() . '.' . $image->getClientOriginalExtension());
            $filepath = Storage::disk('public')->putFileAs('/images', $image, $name);
            $data['photos'][] = $filepath;
        }

        $task->update($data);
        $this->service->syncCustomFields($task);

        return response()->json(['success' => true, 'message' => 'Successfully Updated', 'task' => $task]);

    }

    /**
     * @OA\DELETE(
     *     path="/api/for_del_new_task/{task}",
     *     tags={"Search"},
     *     summary="Delete Task",
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
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function deletetask(Task $task)
    {
        $task->delete();
        CustomFieldsValue::where('task_id', $task)->delete();

        if ($task) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully Deleted'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Not Deleted'
        ], 404);

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
    public function updateName(TaskNameRequest $request, Task $task)
    {
        return $this->success($this->update_task_service->updateName($task, $request->validated()));
    }

    public function updateCustom(Request $request, Task $task)
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
    public function updateRemote(TaskRemoteRequest $request, Task $task)
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
    public function updateAddress(TaskAddressRequest $request, Task $task)
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
    public function updateDate(TaskDateRequest $request, Task $task)
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
    public function updateBudget(TaskBudgetRequest $request, Task $task)
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
    public function updateNote(TaskNoteRequest $request, Task $task)
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
    public function updateUploadImages(Request $request, Task $task)
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
    public function updateContacts(TaskContactsRequest $request, Task $task)
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
    public function updateVerify(TaskVerificationRequest $request, Task $task)
    {
        return $this->update_task_service->verification($task, $request->validated());
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
    public function complain(TaskComplaintRequest $request, Task $task)
    {
        $data = $request->validated();
        $data['task_id'] = $task->id;
        $data['user_id'] = auth()->id();
        $compliant = Compliance::query()->create($data);
        $data['id'] = $compliant->id;
        $data['complaint'] = $compliant->text;
        $data['user_name'] = auth()->user()->name;
        $data['task_name'] = $task->name;
        if (setting('site.bot_token') && setting('site.channel_username'))
            (new TelegramService())->sendMessage($data);
        return response()->json([
            'success' => true,
            'message' => trans('trans.Complaint is sent.')
        ]);
    }
}
