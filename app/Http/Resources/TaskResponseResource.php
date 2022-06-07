<?php

namespace App\Http\Resources;

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
            'user' => new UserInTaskResource($this->performer),
            'budget' => $this->price,
            'description' =>$this->description,
            'created_at' =>$this->created_at,
        ];
    }
}
