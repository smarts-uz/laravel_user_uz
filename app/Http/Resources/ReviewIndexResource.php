<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'user' => new PerformerIndexResource($this->user),
            'description' => $this->description,
            'good_bad' => $this->good_bad,
            'task'=>[
                'id' => $this->task_id,
                'name' => $this->task,
            ],
            'created_at' => $this->created_at
        ];
    }
}
