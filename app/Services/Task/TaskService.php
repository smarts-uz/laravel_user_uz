<?php

namespace App\Services\Task;


use Carbon\Carbon;
use App\Services\{CustomService, TelegramService, Response};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JsonException;
use App\Http\Resources\{PerformerResponseResource,
    SameTaskResource,
    TaskAddressResource,
    TaskPaginationResource,
    TaskResponseResource};
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};
use App\Models\{Compliance, ComplianceType, Task, TaskResponse, User};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    use Response;
    public ResponseService $response_service;
    public const SOME_TASK_LIMIT = 10;

    public function __construct()
    {
        $this->response_service = new ResponseService();
    }

    /**
     * Function  taskIncrement
     * @param int $user_id
     * @param int $task_id
     */
    public function taskIncrement(int $user_id, int $task_id): void
    {
        $viewed_tasks = Cache::get('user_viewed_tasks' . $user_id) ?? [];
        if (!in_array($task_id, $viewed_tasks)) {
            $viewed_tasks[] = $task_id;
        }
        Cache::put('user_viewed_tasks' . $user_id, $viewed_tasks);
        $task = Task::find($task_id);
        $task->increment('views');
    }

    /**
     * Function  taskIndex
     * @param int $task_id
     * @return  array[]|JsonResponse
     */
    public function taskIndex(int $task_id): array|JsonResponse
    {
        $task = Task::where('id', $task_id)->first();
        $val = $task->photos;
        if(!empty($task)) {
            $photos = (!empty($task->photos)) ? array_map(function ($val) {return asset('storage/uploads/' . $val);}, json_decode($task->photos) ?? []) : [];
            $user_response = TaskResponse::query()
                ->where('task_id', $task->id)
                ->where('performer_id', \auth()->guard('api')->id())
                ->first();
            $performer_response = TaskResponse::query()
                ->where('task_id', $task->id)
                ->where('performer_id', $task->performer_id)
                ->first();

            return ['data' => [
                'id' => $task->id,
                'name' => $task->name,
                'address' => (!empty($task->addresses)) ? TaskAddressResource::collection($task->addresses) : [],
                'date_type' => $task->date_type,
                'start_date' => $task->start_date,
                'end_date' => $task->end_date,
                'budget' => $task->budget,
                'description' => $task->description,
                'phone' => $task->phone,
                'performer_id' => $task->performer_id,
                'performer' => (!empty($performer_response)) ? new PerformerResponseResource($performer_response) : null,
                'other'=> $task->category->name === "Что-то другое" || $task->category->name === "Boshqa narsa",
                'parent_category_name'=>$task->category->parent->getTranslatedAttribute('name', app()->getLocale(), 'ru'),
                'category_name' => $task->category->getTranslatedAttribute('name', app()->getLocale(), 'ru'),
                'category_id' => $task->category_id,
                'current_user_response' => (bool)$user_response,
                'responses_count' => $task->responses()->count(),
                'user' => $task->user ? $this->userInTask($task->user) : [],
                'views' => $task->views,
                'status' => $task->status,
                'oplata' => $task->oplata,
                'docs' => $task->docs,
                'created_at' => $task->created,
                'custom_fields' => (new CustomFieldService())->getCustomFieldsByRoute($task->id, 'custom')['custom_fields'] ?? [],
                'photos' => $photos,
                'performer_review' => $task->performer_review,
                'response_price' => setting('admin.pullik_otklik',2000),
                'free_response' => setting('admin.bepul_otklik',3000)
            ]];

        }

        $data = ['data' => [
            'success' => true,
            'message' => __('Задача не найдена')
        ]];
        return response()->json($data);
    }

    /**
     * @param $user
     * @return array
     */
    public function userInTask($user): array
    {
        $lastSeen = (new CustomService)->lastSeen($user);
        return !empty($user) ? [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => asset('storage/'.$user->avatar),
            'phone_number' => (new CustomService)->correctPhoneNumber($user->phone_number),
            'degree' => $user->phone_number,
            'likes' => $user->review_good,
            'dislikes' => $user->review_bad,
            'stars' => $user->review_rating,
            'last_seen' => $lastSeen,
        ]: [];
    }

    /**
     * Shu $task categoriyasiga oid o'xshash tasklarni qaytaradi
     * @param $taskId
     * @return AnonymousResourceCollection
     */
    public function same_tasks($taskId): AnonymousResourceCollection
    {
        $task = Task::find($taskId);
        $tasks = $task->category->tasks()->where('id', '!=', $taskId);
        $tasks = $tasks->where('status', Task::STATUS_OPEN)->take(self::SOME_TASK_LIMIT)->orderByDesc('created_at')->get();
        return SameTaskResource::collection($tasks);
    }

    /**
     * Shu $taskga otklik qilganlarni userlarni qaytaradi
     * @param $filter
     * @param $taskId
     * @return AnonymousResourceCollection
     */
    public function responses($filter, $taskId): AnonymousResourceCollection
    {
        $task = Task::find($taskId);
        if ($task->user_id === auth()->id()) {
            $responses = match ($filter) {
                'rating' => TaskResponse::query()->select('task_responses.*')->join('users', 'task_responses.performer_id', '=', 'users.id')
                    ->where('task_responses.task_id', '=', $taskId)->orderByDesc('users.review_rating'),
                'date' => $task->responses()->orderByDesc('created_at'),
                'price' => $task->responses()->orderBy('price'),
                default => $task->responses(),
            };
        } else {
            $responses = $task->responses()->where('performer_id', auth()->id());
        }
        $responses->where('performer_id', '!=', $task->performer_id);
        return TaskResponseResource::collection($responses->paginate(5));
    }

    /**
     * Bu method app orqali taskka otklik tashlash
     * @param $task
     * @param $user
     * @param $data
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function response_store($taskId, $user, $data): JsonResponse
    {
        $task = Task::find($taskId);
        switch (true) {
            case ((int)$task->user_id === (int)$user->id) :
                return $this->fail(null, trans('trans.your task'));
            case ((int)$user->role_id !== User::ROLE_PERFORMER) :
                return $this->fail(1, trans('trans.not performer')); // 1 -> for open become performer page in app
            case (!($user->is_phone_number_verified)) :
                return $this->fail(null, trans('trans.verify phone'));
        }
        $response = $this->response_service->store($data, $task, $user);

        return response()->json($response);
    }

    /**
     * Task Status changed from cancel to open
     * @param $taskId
     * @param $authId
     * @return JsonResponse
     */
    public function taskStatusUpdate($taskId, $authId): JsonResponse
    {
        $task = Task::find($taskId);
        if ($task->user_id !== $authId){
            return response()->json([
                'success' => false,
                "message" => __('Задача не найдена')
            ], 403);
        }
        $task->status = Task::STATUS_OPEN;
        $task->save();
        return response()->json([
            'success' => true,
            'message' => __('Создано успешно'),
            'data' => $task
        ]);
    }

    /**
     * Get My Tasks Count
     * @param $user
     * @param $is_performer
     * @return JsonResponse
     */
    public function my_tasks_count($user, $is_performer): JsonResponse
    {
        if ($is_performer) {
            $column = 'performer_id';
        } else {
            $column = 'user_id';
        }
        $open_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_OPEN)->count(), 'status' => Task::STATUS_OPEN];
        $in_process_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_IN_PROGRESS)->count(), 'status' => Task::STATUS_IN_PROGRESS];
        $complete_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_COMPLETE)->count(), 'status' => Task::STATUS_COMPLETE];
        $cancelled_tasks = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_CANCELLED)->count(), 'status' => Task::STATUS_CANCELLED];
        $without_reviews = ['count' => Task::query()->where($column, $user->id)->where('status', Task::STATUS_NOT_COMPLETED)->count(), 'status' => Task::STATUS_NOT_COMPLETED];
        $all = ['count' => $open_tasks['count'] + $in_process_tasks['count'] + $complete_tasks['count'] + $cancelled_tasks['count'] + $without_reviews['count'], 'status' => 0];

        return response()->json(['success' => true, 'data' => compact('open_tasks', 'in_process_tasks', 'complete_tasks', 'cancelled_tasks', 'without_reviews', 'all')]);
    }

    /**
     * Get My Tasks
     * @param $user
     * @param $is_performer
     * @param $status
     * @return TaskPaginationResource
     */
    public function my_tasks_all($user, $is_performer, $status): TaskPaginationResource
    {
        $column = $is_performer ? 'performer_id' : 'user_id';
        $tasks = Task::query()->where($column, $user->id);

        if ($status) {
            $tasks = $tasks->where('status', $status);
        }
        else {
            $tasks = $tasks->where('status', '!=', 0);
        }
        return new TaskPaginationResource($tasks->orderByDesc('created_at')->paginate());
    }

    /**
     * Taskka shikoyat qoldirish
     * @param $data
     * @param $user
     * @param $taskId
     * @return JsonResponse
     */
    public function taskComplain($data, $user, $taskId): JsonResponse
    {
        $task = Task::find($taskId);
        $data['task_id'] = $taskId;
        $data['user_id'] = $user->id;
        /** @var Compliance $compliant */
        $compliant = Compliance::query()->create($data);
        $data['id'] = $compliant->id;
        $data['complaint'] = $compliant->text;
        $data['user_name'] = $user->name;
        $data['task_name'] = $task->name;
        if (setting('site.bot_token','') && setting('site.channel_username','')) {
            (new TelegramService())->sendMessage($data);
        }
        return response()->json([
            'success' => true,
            'message' => trans('trans.Complaint is sent.'),
            'data' => $data
        ]);
    }

    /**
     * complain Types
     * @return array
     */
    public function complainTypes(): array
    {
        $complainTypes = ComplianceType::all();
        $data = [];
        foreach ($complainTypes as $complainType) {
            $data[] = [
                'id' => $complainType->id,
                'name' => $complainType->getTranslatedAttribute('name')
            ];
        }
        return $data;
    }

    /**
     * Userning tasklarini qaytaradi
     * @param $user_id
     * @param $status
     * @return TaskPaginationResource
     */
    public function performer_tasks($user_id, $status): TaskPaginationResource
    {
        if((int)$status === 1){
            $tasks = Task::where('user_id', $user_id)->where('status', Task::STATUS_COMPLETE);
        }else{
            $tasks = Task::where('performer_id', $user_id)->where('status', Task::STATUS_COMPLETE);
        }
        return new TaskPaginationResource($tasks->orderByDesc('created_at')->paginate());
    }

    /**
     * Get Performer all Tasks
     * @param $user_id
     * @return TaskPaginationResource
     */
    public function all_tasks($user_id): TaskPaginationResource
    {
        $statuses = [
            Task::STATUS_OPEN,
            Task::STATUS_RESPONSE,
            Task::STATUS_IN_PROGRESS,
            Task::STATUS_COMPLETE,
            Task::STATUS_NOT_COMPLETED,
            Task::STATUS_CANCELLED];
        $tasks = Task::where('user_id', $user_id)->whereIn('status', $statuses);

        return new TaskPaginationResource($tasks->orderByDesc('created_at')->paginate());
    }
}
