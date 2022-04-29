<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this->user;
        $task = Task::query()->find($this->task_id);
        if ($task->user_id == $user->id)
            $performer = 0;
        else
            $performer =1;
        return [
            'id' => $this->id,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'last_seen' => $user->last_seen,
                'review_good' => $user->review_good,
                'review_bad' => $user->review_bad,
                'rating' => $user->review_rating,
                'avatar' => $user->avatar
            ],
            'description' => $this->description,
            'good_bad' => $this->good_bad,
            'performer' => $performer,
            'task' => [
                'name' => $task->name,
                'description' => $task->description
            ],
            'created_at' => $this->created_at
        ];
    }
}
