<?php

namespace App\Services\Task;


use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\{PerformerResponseResource,
    SameTaskResource,
    TaskAddressResource,
    TaskResponseResource,
    UserInTaskResource};
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Models\{Task, TaskResponse, User};
use App\Services\Response;
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
     *
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
     *
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
                'user' => $task->user ? new UserInTaskResource($task->user) : [],
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
     * Shu $task categoriyasiga oid o'xshash tasklarni qaytaradi
     * @param $task
     * @return AnonymousResourceCollection
     */
    public function same_tasks($task): AnonymousResourceCollection
    {
        $tasks = $task->category->tasks()->where('id', '!=', $task->id);
        $tasks = $tasks->where('status', Task::STATUS_OPEN)->take(self::SOME_TASK_LIMIT)->orderByDesc('created_at')->get();
        return SameTaskResource::collection($tasks);
    }

    /**
     * Shu $taskga otklik qilganlarni userlarni qaytaradi
     * @param $filter
     * @param $task
     * @return AnonymousResourceCollection
     */
    public function responses($filter, $task): AnonymousResourceCollection
    {
        if ($task->user_id === auth()->id()) {
            $responses = match ($filter) {
                'rating' => TaskResponse::query()->select('task_responses.*')->join('users', 'task_responses.performer_id', '=', 'users.id')
                    ->where('task_responses.task_id', '=', $task->id)->orderByDesc('users.review_rating'),
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
     */
    public function response_store($task, $user, $data): JsonResponse
    {
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
}
