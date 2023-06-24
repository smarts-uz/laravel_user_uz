<?php

namespace App\Http\Resources;

use App\Services\Task\TaskService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

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
            'addresses' => (new TaskService)->taskAddress($this->addresses),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'budget' => $this->budget,
            'remote' => $this->remote,
            'oplata' => $this->oplata,
            'status' => $this->status,
            'category_icon' => asset('storage/'.$this->category->ico),
            'viewed' => in_array($this->id, Cache::get('user_viewed_tasks' . auth()->guard('api')->id()) ?? []) ?? false
        ];
    }
}
