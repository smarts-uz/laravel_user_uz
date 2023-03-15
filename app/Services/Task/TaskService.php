<?php

namespace App\Services\Task;


use App\Http\Resources\{PerformerResponseResource, TaskAddressResource, UserInTaskResource};
use App\Models\{Task, TaskResponse, User};
use App\Services\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    use Response;
    public ResponseService $response_service;

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

        }

        $data = ['data' => [
            'success' => true,
            'message' => __('Задача не найдена')
        ]];
        return response()->json($data);
    }

    /**
     *
     * Function
     * @param $address
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


}
