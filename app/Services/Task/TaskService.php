<?php

namespace App\Services\Task;


use App\Http\Resources\PerformerResponseResource;
use App\Http\Resources\SameTaskResource;
use App\Http\Resources\TaskAddressResource;
use App\Http\Resources\TaskIndexResource;
use App\Http\Resources\TaskResponseResource;
use App\Http\Resources\UserInTaskResource;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\User;
use App\Services\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    use Response;
    public $response_service;

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
    public function taskIndex($task_id) {
        $task = Task::where('id', (int)$task_id)->first();
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

            $data = ['data' => [
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
                'response_price' => setting('admin.pullik_otklik'),
                'free_response' => setting('admin.bepul_otklik')
            ]];
            return $data;

        } else {
            $data = ['data' => [
                'success' => true,
                'message' => __('Задача не найдена')
            ]];
            return response()->json($data);
        }
    }

    /**
     *
     * Function  same_tasks
     * @param $task_id
     * @param $limit
     * @return  array[]
     */
    public function same_tasks($task_id, $limit): array
    {
        $tasks = Task::with('category')->where('id', '!=', $task_id)
        ->where('status', Task::STATUS_OPEN)->take($limit)
        ->orderByDesc('created_at')->get();
        $data = [];
        foreach ($tasks as $task) {
            $data[] = [
                'id' => $task->id,
                'name' => $task->name,
                'address' => $task->addresses ? $this->address($task->addresses) : __('udalyonka'),
                'budget' => $task->budget,
                'image' => asset('storage/'.$task->category->ico),
                'oplata' => $task->oplata,
                'start_date' => $task->start_date
            ];
        }

        return ['data' => $data];
    }

    /**
     *
     * Function
     * @param $addres
     * @return  array
     */
    public function address($address): array
    {
        return [
            'location' => $address->location,
            'longitude' => $address->longitude,
            'latitude' => $address->latitude,
        ];
    }

    /**
     *
     * Function  responses
     * @param $task_id
     * @param $auth_id
     * @param $filter
     * @return  array[]
     */
    public function responses($task_id, $auth_id, $filter): array
    {
        $task = Task::select('user_id', 'id', 'performer_id')->find($task_id);
        if ($task->user_id === $auth_id) {
            switch ($filter) {
                case 'rating' :
                    $responses = TaskResponse::query()->select('task_responses.*')->join('users', 'task_responses.performer_id', '=', 'users.id')
                        ->where('task_responses.task_id', '=', $task_id)->orderByDesc('users.review_rating');
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
            $responses = $task->responses()->where('performer_id', $auth_id);
        }
        $responses->where('performer_id', '!=', $task->performer_id)->paginate(5);

        $data = [];
        foreach ($responses as $respons) {
            $data = [
                'id' => $respons->id,
                'user' => new UserInTaskResource($respons->performer),
                'budget' => $respons->price,
                'description' =>$respons->description,
                'created_at' =>$respons->created,
                'not_free' => $respons->not_free
            ];
        }

        return ['data' => $data];
    }

    public function response_store($task_id, $user, $data)
    {
        $task = Task::find($task_id);
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

    public function selectPerformer($response)
    {
        if (!$response->task) {
            return response()->json([
                'success' => false,
                'message' => __('Задача не найдена')
            ]);
        }
        $this->response_service->selectPerformer($response);
        return response()->json(['success' => true]);
    }

    public function taskStatusUpdate($task_id, $auth_id)
    {
        $task = Task::select('user_id')->find($task_id);
        if ($task->user_id !== $auth_id){
            return response()->json([
                'success' => false,
                "message" => __('Задача не найдена')
            ], 403);
        }
        $task->status = Task::STATUS_OPEN;
        $task->save();
        return response()->json([
            'success' => true,
            'message' => __('Создано успешно')
        ]);
    }
}
