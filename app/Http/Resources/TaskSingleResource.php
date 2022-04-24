<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskSingleResource extends JsonResource
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
            'name'=> $this->name,
            'addresses' => TaskAddressResource::collection($this->addresses),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'budget' => $this->budget,
            'category_icon' => asset('storage/'.$this->category->ico)
        ];
    }
}
