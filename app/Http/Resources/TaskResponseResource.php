<?php

namespace App\Http\Resources;

use App\Services\Task\TaskService;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return
        [
            'id' => $this->id,
            'user' => (new TaskService)->userInTask($this->performer),
            'budget' => $this->price,
            'description' =>$this->description,
            'created_at' =>$this->created,
            'not_free' => $this->not_free
        ];
    }
}
