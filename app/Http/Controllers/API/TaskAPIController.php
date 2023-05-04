<?php

namespace App\Http\Controllers\API;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Http\Requests\Api\{TaskAddressRequest,
    TaskBudgetRequest,
    TaskComplaintRequest,
    TaskContactsRequest,
    TaskCustomRequest,
    TaskDateRequest,
    TaskNameRequest,
    TaskNoteRequest,
    TaskRemoteRequest,
    TaskResponseRequest,
    TaskUpdateAddressRequest,
    TaskUpdateBudgetRequest,
    TaskUpdateContactRequest,
    TaskUpdateDateRequest,
    TaskUpdateNoteRequest,
    TaskUpdateRemoteRequest,
    TaskUpdateVerifyRequest,
    TaskVerificationRequest};
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
     *     path="/api/same-tasks/{taskId}",
     *     tags={"Task"},
     *     summary="Same tasks by Task ID",
     *     description="[**Telegram :** https://t.me/c/1334612640/177](https://t.me/c/1334612640/177).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi yoziladi",
     *          name="taskId",
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
    public function same_tasks($taskId): AnonymousResourceCollection
    {
        return $this->task_service->same_tasks($taskId);
    }

    /**
     * @OA\Get(
     *     path="/api/responses/{taskId}",
     *     tags={"Task"},
     *     summary="Response tasks",
     *     description="[**Telegram :** https://t.me/c/1334612640/180](https://t.me/c/1334612640/180).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi yoziladi",
     *          name="taskId",
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
    public function responses(Request $request, $taskId): AnonymousResourceCollection
    {
       $filter = $request->get('filter');
       return $this->task_service->responses($filter, $taskId);
    }

    /**
     * @OA\Post(
     *     path="/api/task/{taskId}/response",
     *     tags={"Responses"},
     *     summary="Send Response",
     *     description="[**Telegram :** https://t.me/c/1334612640/222](https://t.me/c/1334612640/222).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi yoziladi",
     *          name="taskId",
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
     *                    description="javob tavsifi yoziladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="price",
     *                    description="javob narxi yoziladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="not_free",
     *                    description="0 - bepul, 1 - pullik",
     *                    enum={"0","1"},
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
    public function response_store($taskId, TaskResponseRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->validated();
        return $this->task_service->response_store($taskId, $user, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/select-performer/{responseId}",
     *     tags={"Responses"},
     *     summary="Select performer",
     *     description="[**Telegram :** https://t.me/c/1334612640/181](https://t.me/c/1334612640/181).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifaga qoldirilgan javob idsi yoziladi",
     *          name="responseId",
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
    public function selectPerformer($responseId): JsonResponse
    {
        $response = TaskResponse::find($responseId);
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
     *     path="/api/task-status-update/{taskId}",
     *     tags={"Task"},
     *     summary="Task Status changed from cancel to open",
     *     description="[**Telegram :** https://t.me/c/1334612640/182](https://t.me/c/1334612640/182).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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

    public function taskStatusUpdate($taskId): JsonResponse
    {
        $authId = auth()->id();
        return $this->task_service->taskStatusUpdate($taskId, $authId);
    }

    /**
     * @OA\Get(
     *     path="/api/tasks-filter",
     *     tags={"Task"},
     *     summary="Task filter",
     *     description="[**Telegram :** https://t.me/c/1334612640/158](https://t.me/c/1334612640/158).",
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
     *          description="kenglik bo'yicha filter",
     *          name="lat",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="uzunlik bo'yicha filter",
     *          name="long",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="vazifa budjeti kiritiladi",
     *          name="budget",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="masofaviy vazifa bo'lsa true, bo'lmasa false bo'ladi",
     *          name="is_remote",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="javob qoldirilgan vazifa bo'lsa true, bo'lmasa false bo'ladi",
     *          name="without_response",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="radius bo'yicha filter",
     *          name="difference",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="vazifa nomi bo'yicha qidirish",
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
     *     path="/api/task/{taskId}",
     *     tags={"Task"},
     *     summary="Get Task By ID",
     *     description="[**Telegram :** https://t.me/c/1334612640/175](https://t.me/c/1334612640/175).",
     *     @OA\Parameter(
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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
    public function task($taskId)
    {
        if (auth()->guard('api')->check()) {
            $user_id = auth()->guard('api')->id();
            (new TaskService)->taskIncrement($user_id, $taskId);
        }

        return (new TaskService)->taskIndex($taskId);
    }

    /**
     * @OA\Post (
     *     path="/api/user/{userId}",
     *     tags={"Task"},
     *     summary="User active task and active step null",
     *     description="[**Telegram :** https://t.me/c/1334612640/176](https://t.me/c/1334612640/176).",
     *     @OA\Parameter (
     *          in="path",
     *          description="Foydaluvchi idsi kiritiladi",
     *          name="userId",
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
    public function active_task_null($userId): JsonResponse
    {
        $user = User::find($userId);
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
     *     description="[**Telegram :** https://t.me/c/1334612640/183](https://t.me/c/1334612640/183).",
     *     @OA\Parameter(
     *          in="query",
     *          description="0 yoki 1, 0 bo'lsa foydalanuvchi yaratgan vazifalari, 1 bo'lsa ijrochi bo'lgan vazifalari",
     *          name="is_performer",
     *          required=false,
     *          @OA\Schema(
     *              enum={"0","1"},
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
     *     description="[**Telegram :** https://t.me/c/1334612640/184](https://t.me/c/1334612640/184).",
     *     @OA\Parameter(
     *          in="query",
     *          description="0 yoki 1, 0 bo'lsa foydalanuvchi yaratgan vazifalari, 1 bo'lsa ijrochi bo'lgan vazifalari",
     *          name="is_performer",
     *          required=false,
     *          @OA\Schema(
     *              enum={"0","1"},
     *              type="string"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          description="vazifa holati kiritiladi",
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
     *     description="[**Telegram :** https://t.me/c/1334612640/117](https://t.me/c/1334612640/117).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    description="vazifaning nomi kiritiladi",
     *                    property="name",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="Child kategoriya id yoziladi",
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
        return $this->success($this->create_task_service->name_store($name, $category_id, $user));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/custom",
     *     tags={"Task Create"},
     *     summary="Task create custom",
     *     description="[**Telegram :** https://t.me/c/1334612640/118](https://t.me/c/1334612640/118).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="vazifa id kiritiladi",
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
     *     description="[**Telegram :** https://t.me/c/1334612640/119](https://t.me/c/1334612640/119).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="vazifa id kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="radio",
     *                    description="Agar masofaviy bo'lsa - remote, manzil bo'yicha bo'lsa - address tanlanadi",
     *                    enum={"remote","address"},
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
     *     description="[**Telegram :** https://t.me/c/1334612640/120](https://t.me/c/1334612640/120).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="vazifa id kiritiladi",
     *                    type="integer",
     *                 ),
     *                  @OA\Property (
     *                    property="points[]",
     *                    type="array",
     *                    @OA\Items(
     *                      type="object",
     *                      @OA\Property(
     *                          property="location",
     *                          description="Manzil kiritiladi",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="latitude",
     *                          description="kenglik kiritiladi",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="longitude",
     *                          description="uzunlik kiritiladi",
     *                          type="string"
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
     *     description="[**Telegram :** https://t.me/c/1334612640/121](https://t.me/c/1334612640/121).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="vazifa idsi kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="date_type",
     *                    description="vazifa boshlanish vaqti kiritilsa 1, tugash vaqti kiritilsa 2, ikkalasi ham kiritilsa 3 tanlanadi",
     *                    enum={"1","2","3"},
     *                    type="string",
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
     *     description="[**Telegram :** https://t.me/c/1334612640/122](https://t.me/c/1334612640/122).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="vazifa idsi kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    description="Vazifa uchun narx kiritiladi",
     *                    property="amount",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    description="Naqt yoki plastik (0 yoki 1)",
     *                    property="budget_type",
     *                    enum={"0","1"},
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
    public function budget(TaskBudgetRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->budget_store($request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/note",
     *     tags={"Task Create"},
     *     summary="Task create note",
     *     description="[**Telegram :** https://t.me/c/1334612640/123](https://t.me/c/1334612640/123).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="vazifa id kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="description",
     *                    description="vazifa uchun tavsif yoziladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="docs",
     *                    description="hujjatlar olinishi kerak bo'lsa - 1,kerak bo'lmasa - 0 tanlanadi",
     *                    enum={"0","1"},
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
    public function note(TaskNoteRequest $request): JsonResponse
    {
        return $this->success($this->create_task_service->note_store($request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/create-task/images",
     *     tags={"Task Create"},
     *     summary="Task create images",
     *     description="[**Telegram :** https://t.me/c/1334612640/124](https://t.me/c/1334612640/124).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="vazifa id kiritiladi",
     *                    type="integer",
     *                 ),
     *                @OA\Property (
     *                    property="images[]",
     *                    type="array",
     *                    @OA\Items(
     *                      type="file",
     *                      @OA\Property(
     *                          property="images",
     *                          description="vazifa uchun rasm kiritiladi",
     *                      ),
     *                    ),
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
     *     description="[**Telegram :** https://t.me/c/1334612640/125](https://t.me/c/1334612640/125).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="vazifa idsi kiritiladi",
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
     * @throws \JsonException
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
     *     description="[**Telegram :** https://t.me/c/1334612640/126](https://t.me/c/1334612640/126).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="vazifa id kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="phone_number",
     *                    description="telefon raqam kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="sms_otp",
     *                    description="Telefonga kelgan SMS kod kiritiladi",
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
     *      path="/api/update-task/{taskId}/name",
     *      tags={"Task Update"},
     *      summary="Task update name",
     *     description="[**Telegram :** https://t.me/c/1334612640/141](https://t.me/c/1334612640/141).",
     *      @OA\Parameter (
     *          in="path",
     *          description="vazifa id kiritiladi",
     *          name="taskId",
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
     *                      description="vazifa nomi kiritiladi",
     *                      type="string",
     *                  ),
     *                  @OA\Property (
     *                      property="category_id",
     *                      description="vazifa uchun child kategoriya idsi kiritiladi",
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
    public function updateName(TaskNameRequest $request, $taskId): JsonResponse
    {
        return $this->success($this->update_task_service->updateName($taskId, $request->validated()));
    }

    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/custom",
     *     tags={"Task Update"},
     *     summary="Update task custom fields",
     *     description="[**Telegram :** https://t.me/c/1334612640/185](https://t.me/c/1334612640/185).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa id kiritiladi",
     *          name="taskId",
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
    public function updateCustom(Request $request, $taskId): JsonResponse
    {
        return $this->success($this->update_task_service->updateCustom($taskId, $request));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/remote",
     *     tags={"Task Update"},
     *     summary="Task update remote",
     *     description="[**Telegram :** https://t.me/c/1334612640/142](https://t.me/c/1334612640/142).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa id kiritiladi",
     *          name="taskId",
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
     *                    description="Agar masofaviy ish bolsa - remote, manzil bo'yicha bo'lsa - address kiritiladi",
     *                    enum={"remote","address"},
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
    public function updateRemote(TaskUpdateRemoteRequest $request, $taskId): JsonResponse
    {
        $data = $request->validated();
        return $this->success($this->update_task_service->updateRemote($taskId, $data));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/address",
     *     tags={"Task Update"},
     *     summary="Task update address",
     *     description="[**Telegram :** https://t.me/c/1334612640/257](https://t.me/c/1334612640/257).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa id kiritiladi",
     *          name="taskId",
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
     *                  @OA\Property (
     *                    property="points[]",
     *                    type="array",
     *                    @OA\Items(
     *                      type="object",
     *                      @OA\Property(
     *                          property="location",
     *                          description="Manzil kiritiladi",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="latitude",
     *                          description="kenglik kiritiladi",
     *                          type="numeric"
     *                      ),
     *                      @OA\Property(
     *                          property="longitude",
     *                          description="uzunlik kiritiladi",
     *                          type="numeric"
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
    public function updateAddress(TaskUpdateAddressRequest $request, $taskId): JsonResponse
    {
        $data = $request->validated();
        return $this->success($this->update_task_service->updateAddress($taskId, $data));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/date",
     *     tags={"Task Update"},
     *     summary="Task update date",
     *     description="[**Telegram :** https://t.me/c/1334612640/143](https://t.me/c/1334612640/143).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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
     *                    description="vazifa boshlanish vaqti kiritilsa 1, tugash vaqti kiritilsa 2, ikkalasi ham kiritilsa 3 tanlanadi",
     *                    enum={"1","2","3"},
     *                    type="string",
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
    public function updateDate(TaskUpdateDateRequest $request, $taskId): JsonResponse
    {
        return $this->success($this->update_task_service->updateDate($taskId, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/budget",
     *     tags={"Task Update"},
     *     summary="Task update budget",
     *     description="[**Telegram :** https://t.me/c/1334612640/144](https://t.me/c/1334612640/144).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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
     *                    description="Narxi yoziladi",
     *                    property="amount",
     *                    type="number",
     *                 ),
     *                 @OA\Property (
     *                    description="Naqt yoki plastik (0 yoki 1)",
     *                    property="budget_type",
     *                    enum={"0","1"},
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
    public function updateBudget(TaskUpdateBudgetRequest $request, $taskId): JsonResponse
    {
        return $this->success($this->update_task_service->updateBudget($taskId, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/note",
     *     tags={"Task Update"},
     *     summary="Task update note",
     *     description="[**Telegram :** https://t.me/c/1334612640/145](https://t.me/c/1334612640/145).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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
     *                    description="vazifa uchun tavsif kiritiladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="docs",
     *                    description="hujjatlar olinishi kerak bo'lsa - 1,kerak bo'lmasa - 0 tanlanadi",
     *                    enum={"0","1"},
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
    public function updateNote(TaskUpdateNoteRequest $request, $taskId): JsonResponse
    {
        return $this->success($this->update_task_service->updateNote($taskId, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/images",
     *     tags={"Task Update"},
     *     summary="Task update images",
     *     description="[**Telegram :** https://t.me/c/1334612640/146](https://t.me/c/1334612640/146).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa id kiritiladi",
     *          name="taskId",
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
     *                @OA\Property (
     *                    property="images[]",
     *                    type="array",
     *                    @OA\Items(
     *                      type="file",
     *                      @OA\Property(
     *                          property="images",
     *                          description="vazifa uchun rasm kiritiladi",
     *                      ),
     *                    ),
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
    public function updateUploadImages(Request $request, $taskId): JsonResponse
    {
        return $this->update_task_service->updateImage($taskId, $request);
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/contacts",
     *     tags={"Task Update"},
     *     summary="Task update contacts",
     *     description="[**Telegram :** https://t.me/c/1334612640/147](https://t.me/c/1334612640/147).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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
     *                    description="vazifaga telefon raqaam kiritiladi",
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
    public function updateContacts(TaskUpdateContactRequest $request, $taskId): JsonResponse
    {
        return $this->success($this->update_task_service->updateContact($taskId, $request->validated()));
    }


    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/verify",
     *     tags={"Task Update"},
     *     summary="Task update verify",
     *     description="[**Telegram :** https://t.me/c/1334612640/258](https://t.me/c/1334612640/258).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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
     *                    description="vazifa uchun telefon raqam kiritiladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="sms_otp",
     *                    description="Telefonga kelgan SMS kod kiritiladi",
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
    public function updateVerify(TaskUpdateVerifyRequest $request, $taskId): JsonResponse
    {
        $data = $request->validated();
        return $this->update_task_service->verification($taskId, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/update-task/{taskId}/delete-image",
     *     tags={"Task Update"},
     *     summary="Task delete images",
     *     description="[**Telegram :** https://t.me/c/1334612640/259](https://t.me/c/1334612640/259).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa idsi kiritiladi",
     *          name="taskId",
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
     *                    property="image",
     *                    description="o'chiriladigan rasm url manzili kiritiladi",
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
    public function deleteImage(Request $request, $taskId): JsonResponse
    {
        return $this->update_task_service->deleteImage($request, $taskId);
    }


    /**
     * @OA\Post(
     *     path="/api/task/{taskId}/complain",
     *     tags={"Complains"},
     *     summary="Task complain",
     *     description="[**Telegram :** https://t.me/c/1334612640/174](https://t.me/c/1334612640/174).",
     *     @OA\Parameter (
     *          in="path",
     *          description="vazifa id kiritiladi",
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
    public function complain(TaskComplaintRequest $request, $taskId): JsonResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        return $this->task_service->taskComplain($data, $user, $taskId);
    }

    /**
     * @OA\Get (
     *     path="/api/complain/types",
     *     tags={"Complains"},
     *     summary="Task complains types",
     *     description="[**Telegram :** https://t.me/c/1334612640/173](https://t.me/c/1334612640/173).",
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
     *     description="[**Telegram :** https://t.me/c/1334612640/179](https://t.me/c/1334612640/179).",
     *     @OA\Parameter(
     *          in="query",
     *          description="foydalanuvchi idsi kiritiladi",
     *          name="user_id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          description="vazifa holati kiritiladi(1 yoki 0)",
     *          name="status",
     *          required=true,
     *          @OA\Schema(
     *              enum={"1","0"},
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
     *     description="[**Telegram :** https://t.me/c/1334612640/228](https://t.me/c/1334612640/228).",
     *     @OA\Parameter(
     *          in="query",
     *          description="foydalanuvchi idsi kiritiladi",
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
