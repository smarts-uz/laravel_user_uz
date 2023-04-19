<?php

namespace App\Http\Controllers\API;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Http\Requests\Api\{TaskAddressRequest, TaskBudgetRequest, TaskComplaintRequest,
    TaskContactsRequest, TaskCustomRequest, TaskDateRequest, TaskNameRequest,
    TaskNoteRequest, TaskRemoteRequest, TaskResponseRequest, TaskVerificationRequest};
use App\Http\Resources\{ComplianceTypeResource,
    TaskSingleResource, TaskPaginationResource};
use Illuminate\{Http\Request,
    Http\JsonResponse, Routing\Controller,
    Validation\ValidationException, Http\Resources\Json\AnonymousResourceCollection};
use App\Models\{User, Task, TaskResponse, ComplianceType};
use App\Services\{Task\TaskService, Task\ResponseService,
    Task\CreateTaskService, Task\FilterTaskService, Task\UpdateTaskService, Response};

class TaskAPIController extends Controller
{
    use Response;

    private TaskService $task_service;
    private ResponseService $response_service;
    private FilterTaskService $filter_service;
    private CreateTaskService $create_task_service;
    private UpdateTaskService $update_task_service;

    public function __construct()
    {
        $this->task_service = new TaskService();
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
     *          description="task id yoziladi",
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
        return $this->task_service->same_tasks($task);
    }

    /**
     * @OA\Get(
     *     path="/api/responses/{task}",
     *     tags={"Task"},
     *     summary="Response tasks",
     *     @OA\Parameter (
     *          in="path",
     *          description="task id yoziladi",
     *          name="task",
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
    public function responses(Request $request, Task $task): AnonymousResourceCollection
    {
       $filter = $request->get('filter');
       return $this->task_service->responses($filter, $task);
    }

    /**
     * @OA\Post(
     *     path="/api/task/{task}/response",
     *     tags={"Responses"},
     *     summary="Send Response",
     *     @OA\Parameter (
     *          in="path",
     *          description="task id yoziladi",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="description",
     *                    description="otklik tavsifi yoziladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="price",
     *                    description="otklik narxi yoziladi",
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
    public function response_store(Task $task, TaskResponseRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->validated();
        return $this->task_service->response_store($task, $user, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/select-performer/{response}",
     *     tags={"Responses"},
     *     summary="Select performer",
     *     @OA\Parameter (
     *          in="path",
     *          description="task response id yoziladi",
     *          name="response",
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
    public function selectPerformer(TaskResponse $response): JsonResponse
    {
        if (!$response->task || auth()->id() === $response->performer_id) {
            return response()->json([
                'success' => false,
                'message' => __('Задача не найдена')
            ]);
        }
        $this->response_service->selectPerformer($response);
        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/task-status-update/{task}",
     *     tags={"Task"},
     *     summary="Task Status changed from cancel to open",
     *     @OA\Parameter (
     *          in="path",
     *          description="task idsi kiritiladi",
     *          name="task",
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

    public function taskStatusUpdate(Task $task): JsonResponse
    {
        $authId = auth()->id();
        return $this->task_service->taskStatusUpdate($task, $authId);
    }

    /**
     * @OA\Get(
     *     path="/api/tasks-filter",
     *     tags={"Task"},
     *     summary="Task filter",
     *     @OA\Parameter (
     *          in="query",
     *          name="categories",
     *          description="[2,3,7] - manashu formatda kiritiladi",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="child_categories",
     *          description="[23,24,25] - manashu formatda kiritiladi",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="latitude bo'yicha filter",
     *          name="lat",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="longitude bo'yicha filter",
     *          name="long",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="task budgeti kiritiladi",
     *          name="budget",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="remote task bo'lsa true, bo'lmasa false bo'ladi",
     *          name="is_remote",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="otklik tashlangan task bo'lsa true, bo'lmasa false bo'ladi",
     *          name="without_response",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="difference bo'yicha filter",
     *          name="difference",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="task nomi bo'yicha qidirish",
     *          name="s",
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
     * )
     */
    public function filter(Request $request): AnonymousResourceCollection
    {
        $tasks = $this->filter_service->filter($request->all());
        return TaskSingleResource::collection($tasks);
    }

    /**
     * @OA\Get(
     *     path="/api/task/{task}",
     *     tags={"Task"},
     *     summary="Get Task By ID",
     *     @OA\Parameter(
     *          in="path",
     *          description="task id kiritiladi",
     *          name="task",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
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
    public function task($task)
    {
        if (auth()->guard('api')->check()) {
            $user_id = auth()->guard('api')->id();
            (new TaskService)->taskIncrement($user_id, $task);
        }

        return (new TaskService)->taskIndex($task);
    }

    /**
     * @OA\Post (
     *     path="/api/user/{user}",
     *     tags={"Task"},
     *     summary="User active task and active step null",
     *     @OA\Parameter (
     *          in="path",
     *          description="user id kiritiladi",
     *          name="user",
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
     *     )
     * )
     */
    public function active_task_null(User $user): JsonResponse
    {
        $user->active_step = null;
        $user->active_task = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('Изменено успешно')
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/my-tasks-count",
     *     tags={"Task"},
     *     summary="Get My Tasks Count",
     *     @OA\Parameter(
     *          in="query",
     *          description="0 yoki 1, 0 bo'lsa user create qilgan tasklari, 1 bo'lsa performer bo'lgan tasklari",
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

        return $this->task_service->my_tasks_count($user, $is_performer);
    }

    /**
     * @OA\Get(
     *     path="/api/my-tasks",
     *     tags={"Task"},
     *     summary="Get My Tasks",
     *     @OA\Parameter(
     *          in="query",
     *          description="0 yoki 1, 0 bo'lsa user create qilgan tasklari, 1 bo'lsa performer bo'lgan tasklari",
     *          name="is_performer",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          description="task statusi kiritiladi",
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
        return $this->task_service->my_tasks_all($user, $is_performer, $status);
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
     *                    description="Taskning nomi kiritiladi",
     *                    property="name",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="Category id yoziladi (faqat parent_idsi bor bo'lishi kerak)",
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
        $data = $request->validated();
        $name = $data['name'];
        $category_id = $data['category_id'];
        $user = auth()->user();
        $user_id = auth()->id();
        return $this->success($this->create_task_service->name_store($name, $category_id, $user, $user_id));
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
     *                    description="task id kiritiladi",
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
     *                    description="task id kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="radio",
     *                    description="Agar masofaviy bolsa - remote, manzil bo`yicha bo`lsa - address deb yozing",
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
     *                    description="task id kiritiladi",
     *                    type="integer",
     *                 ),
     *                  @OA\Property (
     *                    property="points[]",
     *                    type="array",
     *                    @OA\Items(
     *                      type="object",
     *                      @OA\Property(
     *                          property="location",
     *                          description="location kiritiladi",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="latitude",
     *                          description="latitude kiritiladi",
     *                          type="number"
     *                      ),
     *                      @OA\Property(
     *                          property="longitude",
     *                          description="longitude kiritiladi",
     *                          type="number"
     *                      ),
     *                   ),
     *                 )
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
     *                    description="task id kiritiladi",
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
     *                    description="task id kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    description="Narxi",
     *                    property="amount",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    description="Naqt yoki plastik (0 yoki 1)",
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
     *                    description="task id kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="description",
     *                    description="task uchun tavsif yoziladi",
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
     *                    description="task id kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="images",
     *                    description="task uchun rasm kiritiladi",
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
     *                    description="task id kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="phone_number",
     *                    description="telefon raqam kiritiladi",
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
     * @param TaskContactsRequest $request
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function contacts(TaskContactsRequest $request): JsonResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        return $this->success($this->create_task_service->contact_store($data, $user));
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
     *                    description="task id kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="phone_number",
     *                    description="telefon raqam kiritiladi",
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
        $data = $request->validated();
        return $this->create_task_service->verification($data);
    }


    /**
     * @OA\Post(
     *      path="/api/update-task/{task}/name",
     *      tags={"Task Update"},
     *      summary="Task update name",
     *      @OA\Parameter (
     *          in="path",
     *          description="task id kiritiladi",
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
     *                      description="task name kiritiladi",
     *                      type="string",
     *                  ),
     *                  @OA\Property (
     *                      property="category_id",
     *                      description="task child category_id kiritiladi",
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

    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/custom",
     *     tags={"Task Update"},
     *     summary="Update task custom fields",
     *     @OA\Parameter (
     *          in="path",
     *          description="task id kiritiladi",
     *          name="task",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
     *      ),
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
     *          description="task id kiritiladi",
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
     *                    property="radio",
     *                    description="Agar masofaviy ish bolsa - remote, manzil bo`yicha bo`lsa - address deb yozing",
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
     *          description="task id kiritiladi",
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
     *                    property="location",
     *                    description="task location kiritiladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="latitude",
     *                    description="task latitude kiritiladi",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    property="longitude",
     *                    description="task longitude kiritiladi",
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
     *          description="task id kiritiladi",
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
     *          description="task id kiritiladi",
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
     *                    description="Narxi",
     *                    property="amount",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    description="Naqt yoki plastik (0 yoki 1)",
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
     *          description="task id kiritiladi",
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
     *                    property="description",
     *                    description="task uchun tavsif kiritiladi",
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
     *          description="task id kiritiladi",
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
     *                    property="images",
     *                    description="task uchun rasm kiritiladi",
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
     *          description="task id kiritiladi",
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
     *                    property="phone_number",
     *                    description="task phone_number kiritiladi",
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
    public function updateContacts(TaskContactsRequest $request, $task_id): JsonResponse
    {
        return $this->success($this->update_task_service->updateContact($task_id, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/verify",
     *     tags={"Task Update"},
     *     summary="Task update verify",
     *     @OA\Parameter (
     *          in="path",
     *          description="task id kiritiladi",
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
     *                    property="phone_number",
     *                    description="task phone_number kiritiladi",
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
    public function updateVerify(TaskVerificationRequest $request, $task_id): JsonResponse
    {
        $data = $request->validated();
        return $this->update_task_service->verification($task_id, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/update-task/{task}/delete-image",
     *     tags={"Task Update"},
     *     summary="Task delete images",
     *     @OA\Parameter (
     *          in="path",
     *          description="task id kiritiladi",
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
     *                    property="images",
     *                    description="delete qilinadigan image url kiritiladi",
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
    public function deleteImage(Request $request, Task $task): JsonResponse
    {
        return $this->update_task_service->deleteImage($request, $task);
    }


    /**
     * @OA\Post(
     *     path="/api/task/{task}/complain",
     *     tags={"Complains"},
     *     summary="Task complain",
     *     @OA\Parameter (
     *          in="path",
     *          description="task id kiritiladi",
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
     *                    description="shikoyat turi idsi kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="text",
     *                    description="shikoyat matni kiritiladi",
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
        return $this->task_service->taskComplain($data, $user, $task);
    }

    /**
     * @OA\Get (
     *     path="/api/complain/types",
     *     tags={"Complains"},
     *     summary="Task complains types",
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
    public function complainTypes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ComplianceTypeResource::collection(ComplianceType::all())
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/performer-tasks",
     *     tags={"Task"},
     *     summary="Get Performer Tasks",
     *     @OA\Parameter(
     *          in="query",
     *          description="user id kiritiladi",
     *          name="user_id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          description="status kiritiladi(1 yoki 0)",
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
    public function performer_tasks(Request $request): TaskPaginationResource
    {
        $user_id = $request->get('user_id');
        $status = $request->get('status');
        return $this->task_service->performer_tasks($user_id, $status);
    }

    /**
     * @OA\Get(
     *     path="/api/all-tasks",
     *     tags={"Task"},
     *     summary="Get Performer all Tasks",
     *     @OA\Parameter(
     *          in="query",
     *          description="user id kiritiladi",
     *          name="user_id",
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
    public function all_tasks(Request $request): TaskPaginationResource
    {
        $user_id = $request->get('user_id');
        return $this->task_service->all_tasks($user_id);
    }
}
