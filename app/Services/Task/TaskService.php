<?php

namespace App\Services\Task;


use App\Http\Resources\TaskAddressResource;
use App\Http\Resources\TaskIndexResource;
use App\Http\Resources\UserInTaskResource;
use App\Models\Task;
use App\Models\TaskResponse;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    public function taskIncrement($user_id, $task_id) {
        $viewed_tasks = Cache::get('user_viewed_tasks' . $user_id) ?? [];
        if (!in_array($task_id, $viewed_tasks)) {
            $viewed_tasks[] = $task_id;
        }
        Cache::put('user_viewed_tasks' . $user_id, $viewed_tasks);
        $task = Task::find($task_id);
        $task->increment('views');
    }

    public function taskIndex($task_id) {
        $task = Task::where('id', (int)$task_id)->first();
        if(!empty($task)) {
            $photos = array_map(function ($val) {
                return asset('storage/uploads/' . $val);
            },
                json_decode(!empty($task->photos)) ?? []
            );
            $user_response = TaskResponse::query()
                ->where('task_id', $task->id)
                ->where('performer_id', \auth()->guard('api')->id())
                ->first();
            /*$performer_response = TaskResponse::query()
                ->where('task_id', $task->id)
                ->where('performer_id', $task->performer_id)
                ->first();*/
            $data = ['data' => [
                'id' => $task->id,
                'name' => $task->name,
                'address' => TaskAddressResource::collection($task->addresses),
                'date_type' => $task->date_type,
                'start_date' => $task->start_date,
                'end_date' => $task->end_date,
                'budget' => $task->budget,
                'description' => $task->description,
                //'phone' => $this->phone,
                //'performer_id' => $this->performer_id,
                //'performer' => new PerformerResponseResource($performer_response),
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
                'custom_fields' => (new CustomFieldService())->getCustomFieldsByRoute($task->id, 'custom')['custom_fields'],
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
}
