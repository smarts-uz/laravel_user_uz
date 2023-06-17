<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteTaskResource extends JsonResource
{

    public function toArray($request)
    {
        $task = $this->task;
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'task' => new TaskSingleResource($task),
        ];
    }
}
