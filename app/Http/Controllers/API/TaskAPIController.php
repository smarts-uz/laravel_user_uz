<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Api\TaskBudgetRequest;
use App\Http\Requests\Api\TaskContactsRequest;
use App\Http\Requests\Api\TaskCustomRequest;
use App\Http\Requests\Api\TaskFilterRequest;
use App\Http\Requests\Api\TaskNameRequest;
use App\Http\Requests\Api\TaskNoteRequest;
use App\Http\Requests\Api\TaskRemoteRequest;
use App\Http\Requests\Api\V1\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Requests\TaskDateRequest;
use App\Http\Resources\CustomFiledResource;
use App\Http\Resources\SameTaskResource;
use App\Http\Resources\TaskIndexResource;
use App\Http\Resources\TaskPaginationResource;
use App\Http\Resources\TaskResponseResource;
use App\Models\CustomField;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\User;
use App\Services\Response;
use App\Services\Task\CreateService;
use App\Services\Task\CreateTaskService;
use App\Services\Task\FilterTaskService;
use App\Services\Task\ResponseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\CustomFieldsValue;

class TaskAPIController extends Controller
{
    use Response;

    private $service;
    private $response_service;
    private $filter_service;
    private $create_task_service;

    public function __construct()
    {
        $this->service = new CreateService();
        $this->filter_service = new FilterTaskService();
        $this->response_service = new ResponseService();
        $this->create_task_service = new CreateTaskService();

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
    public function responses(Task $task)
    {
        return TaskResponseResource::collection($task->responses);
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
        $response = $this->response_service->store($request, $task);

        return response()->json($response);
    }

    public function selectPerformer(TaskResponse $response)
    {
        $response = $this->response_service->selectPerformer($response);
        return response()->json($response);
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
    public function filter(TaskFilterRequest $request)
    {
        $data = $request->validated();
        $tasks = $this->filter_service->filter($data);

        return new TaskPaginationResource($tasks);
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
        $cancelled_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_COMPLETE_WITHOUT_REVIEWS)->count(), 'status' => Task::STATUS_COMPLETE_WITHOUT_REVIEWS];
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
        $tasks = Task::query()->where($column, $user->id);

        if ($status)
            $tasks = $tasks->where('status', $status);
        else
            $tasks = $tasks->where('status', '!=', 0);

        return new TaskPaginationResource($tasks->paginate());
    }


    /**
     * @OA\Post(
     *     path="/api/create-task/routing",
     *     tags={"Task"},
     *     summary="Routing",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="category_id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="address",
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
    public function routing(Request $request)
    {
        $request->validate(['route' => 'required']);
//        $category = Category::query()->findOrFail($request->get('category_id'));
        $data = [];
        switch ($request->get('route')) {
            case CustomField::ROUTE_NAME:
                $data = $this->create_task_service->name_store($request);
                break;
            case CustomField::ROUTE_CUSTOM:
                $data = $this->create_task_service->custom_store($request);
                break;
            case CustomField::ROUTE_REMOTE:
                $data = $this->create_task_service->remote_store($request);
                break;
            case CustomField::ROUTE_ADDRESS:
                $data = $this->create_task_service->address_store($request);
                break;
            case CustomField::ROUTE_DATE:
                $data = $this->create_task_service->date_store($request);
                break;
            case CustomField::ROUTE_BUDGET:
                $data = $this->create_task_service->budget_store($request);
                break;
            case CustomField::ROUTE_NOTE:
                $data = $this->create_task_service->note_store($request);
                break;
            case CustomField::ROUTE_CONTACTS:
                $data = $this->create_task_service->contact_store($request);
                break;
        }

        return $this->success($data);

    }

    public function name(TaskNameRequest $request)
    {
        return $this->success($this->create_task_service->name_store($request->validated()));
    }

    public function custom(TaskCustomRequest $request)
    {
        return $this->success($this->create_task_service->custom_store($request->validated()));
    }

    public function remote(TaskRemoteRequest $request)
    {
        return $this->success($this->create_task_service->remote_store($request->validated()));
    }

    public function address(Request $request)
    {
        $this->success($this->create_task_service->address_store($request));
    }

    public function date(\App\Http\Requests\Api\TaskDateRequest $request)
    {
        return $this->success($this->create_task_service->date_store($request->validated()));
    }

    public function budget(TaskBudgetRequest $request)
    {
        return $this->success($this->create_task_service->budget_store($request->validated()));
    }

    public function note(TaskNoteRequest $request)
    {
        return $this->success($this->create_task_service->note_store($request->validated()));
    }

    public function contacts(TaskContactsRequest $request)
    {
        return $this->success($this->create_task_service->contact_store($request->validated()));
    }


    public function getFields(Request $request)
    {
        $category = Category::query()->findOrFail($request->get('category_id'));
        $data = [];
        switch ($request->get('route')) {
            case CustomField::ROUTE_NAME:
//                $data = $this->create_task_service->name($category->name);
                break;
            case CustomField::ROUTE_CUSTOM:
                $data = $this->create_task_service->get_custom($category);
                break;
            case CustomField::ROUTE_REMOTE:

                break;
            case CustomField::ROUTE_ADDRESS:
                $data = $this->create_task_service->get_address($category->parent->double_address);
                break;
            case CustomField::ROUTE_BUDGET:
                $data['custom_fields'] = $category->customFieldsInBudget;
                break;
            case CustomField::ROUTE_NOTE:
                $data['custom_fields'] = $category->customFieldsInNote;
                break;
            case CustomField::ROUTE_DATE:
                $data['custom_fields'] = $category->customFieldsInDate;
                break;

        }

        return $this->success($data);
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
}
