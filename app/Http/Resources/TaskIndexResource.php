<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskIndexResource extends JsonResource
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
            'name' => $this->name,
            'address' => json_decode($this->address),
            'address_add' => json_decode($this->address_add),
            'date_type' => $this->date_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'budget' => $this->budget,
            'description' => $this->description,
            'phone' => $this->phone,
            //'category_id' => $this->category_id,
            'category' => $this->category,
            'performer_id' => $this->performer_id,
            //'user_id' => $this->user_id,
            'user' => $this->user,
            'views' => $this->views,
            'status' => $this->status,
            'oplata' => $this->oplata,
            'docs' => $this->docs,
            'photos' => json_decode(asset('storage/'.$this->photos)),
            'task_responses' => $this->responses()->without('task')->get(),
            'same_tasks' => $this->category->tasks()->where('id','!=',$this->id)->where('status', Task::STATUS_OPEN)->take(10)->get(),
        ];
    }
}
