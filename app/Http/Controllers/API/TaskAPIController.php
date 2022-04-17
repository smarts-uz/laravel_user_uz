<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TaskFilterRequest;
use App\Http\Requests\Api\V1\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\SameTaskResource;
use App\Http\Resources\TaskIndexResource;
use App\Http\Resources\TaskResponseResource;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\User;
use App\Services\Task\CreateService;
use App\Services\Task\FilterTaskService;
use App\Services\Task\ResponseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\CustomFieldsValue;

class TaskAPIController extends Controller
{
    private $service;
    private $response_service;
    private $filter_service;

    public function __construct()
    {
        $this->service = new CreateService();
        $this->filter_service = new FilterTaskService();
        $this->response_service = new ResponseService();

    }
    public function same_tasks(Task $task, Request $request)
    {
        $tasks = $task->category->tasks()->where('id','!=',$task->id);
        $tasks = $tasks->where('status', Task::STATUS_OPEN)->take(10)->get();
        return SameTaskResource::collection($tasks);
    }



    public function responses(Task $task)
    {
        return TaskResponseResource::collection($task->responses);
    }

    public function response_store(Task $task, Request $request)
    {
        $response = $this->response_service->store($request,$task);

        return response()->json($response);
    }

    public function selectPerformer(TaskResponse $response)
    {
        $response = $this->response_service->selectPerformer($response);
        return  response()->json($response);
    }

    public function filter(TaskFilterRequest $request)
    {
        $data = $request->validated();
        $tasks =$this->filter_service->filter($data);

        return TaskIndexResource::collection($tasks);
    }




    /**
     * @OA\Get(
     *     path="/api/task/{task}",
     *     tags={"Task"},
     *     summary="Show tasks by ID",
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
     *     path="/api/my-tasks",
     *     tags={"Task"},
     *     summary="Get list of my Tasks",
     *     security={
     *      {"token": {}},
     *     },
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *     )
     * )
     */
    public function my_tasks()
    {
        $user = auth()->user();
        $open_tasks = $user->tasks()->where('status', Task::STATUS_OPEN)->count();
        $in_process_tasks = $user->tasks()->where('status', Task::STATUS_IN_PROGRESS)->count();
        $complete_tasks = $user->tasks()->where('status', Task::STATUS_COMPLETE)->count();
        $cancelled_tasks = $user->tasks()->where('status', Task::STATUS_COMPLETE_WITHOUT_REVIEWS)->count();
        $all = $open_tasks + $in_process_tasks + $complete_tasks + $cancelled_tasks;
        $data = compact('open_tasks', 'complete_tasks', 'cancelled_tasks','in_process_tasks', 'all');

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function my_open_tasks()
    {

        return [];
    }
    /**
     *
     * @OA\Post (
     *     path="/api/task/create",
     *     tags={"Task"},
     *     summary="Add new task",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="address",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="date_type",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="start_date",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="end_date",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="budget",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="category_id",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"Javoxir",
     *                     "address":"Xorazm",
     *                     "date_type":"1",
     *                     "start_date":"2021-05-17 10:00",
     *                     "end_date":"2021-05-17 10:00",
     *                     "budget":10000,
     *                     "description":"Juda zo`r",
     *                     "category_id":"31",
     *                     "phone":"909598654",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="address", type="string", example="address"),
     *              @OA\Property(property="date_type", type="string", example="1"),
     *              @OA\Property(property="start_date", type="string", example="2021-05-17 00:00"),
     *              @OA\Property(property="end_date", type="string", example="2021-05-17 00:00"),
     *              @OA\Property(property="budget", type="integer", example="100000"),
     *              @OA\Property(property="description", type="string", example="Zo`r"),
     *              @OA\Property(property="category_id", type="integer", example="35"),
     *              @OA\Property(property="phone", type="string", example="909598654"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string", example="fail"),
     *          )
     *      ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function create(StoreRequest $request)
    {
        $data = $request->validated();
        $data["user_id"] = auth()->user()->id;

        $images = isset($data['photos'])?$data['images']:[];
        $data['photos'] =  [];
        foreach ($images as $image) {
            $name = md5(\Carbon\Carbon::now().'_'.$image->getClientOriginalName().'.'.$image->getClientOriginalExtension());
            $filepath = Storage::disk('public')->putFileAs('/images',$image, $name);
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
     *
     * @OA\Put (
     *     path="/api/change-task/{task}",
     *     tags={"Task"},
     *     summary="Update Task",
     *     @OA\Parameter(
     *         in="path",
     *         name="task",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="address",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="date_type",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="budget",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="category_id",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "name":"Javoxir",
     *                     "address":"Xorazm viloyati",
     *                     "date_type":"1",
     *                     "budget":12000,
     *                     "description":"Juda zo`r",
     *                     "category_id":"31",
     *                     "phone":"998987456",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Javoxir"),
     *              @OA\Property(property="address", type="string", example="Xorazm viloyati"),
     *              @OA\Property(property="date_type", type="string", example="1"),
     *              @OA\Property(property="budget", type="integer", example="15000"),
     *              @OA\Property(property="description", type="string", example="Juda zo`r"),
     *              @OA\Property(property="category_id", type="integer", example="31"),
     *              @OA\Property(property="phone", type="string", example="998987456"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function changeTask(UpdateRequest $request, Task $task){
        taskGuard($task);
        $data = $request->validated();
        $data = getAddress($data); // шуни комментировать килиб койса swagger да ишлайди

        $images = isset($data['photos'])?$data['images']:[];
        $data['photos'] =  [];
        foreach ($images as $image) {
            $name = md5(\Carbon\Carbon::now().'_'.$image->getClientOriginalName().'.'.$image->getClientOriginalExtension());
            $filepath = Storage::disk('public')->putFileAs('/images',$image, $name);
            $data['photos'][] = $filepath;
        }

        $task->update($data);
        $this->service->syncCustomFields($task);

        return response()->json(['success' => true, 'message' => 'Successfully Updated', 'task' => $task]);

    }


    /**
     * @OA\DELETE(
     *     path="/api/for_del_new_task/{task}",
     *     tags={"Task"},
     *     summary="Delete Task",
     *     security={
     *         {"token": {}}
     *     },
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
    public function deletetask(Task $task)
    {
        $task->delete();
        CustomFieldsValue::where('task_id', $task)->delete();

        if($task){
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
