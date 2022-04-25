<?php

namespace App\Http\Resources;

use App\Services\Task\CustomFieldService;
use Carbon\Carbon;
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
            'address' => TaskAddressResource::collection($this->addresses),
            'date_type' => $this->date_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'budget' => $this->budget,
            'description' => $this->description,
            'phone' => $this->phone,
            'performer_id' => $this->performer_id,
            //'category_id' => $this->category_id,
            'category_name' => $this->category->name,
            'category_id' => $this->category_id,
            'user' => new UserInTaskResource($this->user),
            'views' => $this->views,
            'status' => $this->status,
            'oplata' => $this->oplata,
            'docs' => $this->docs,
            'created_at' => $this->created,
            'custom_fields' => (new CustomFieldService())->getCustomFieldsByRoute($this, ''),
            'photos' => json_decode(asset('storage/'.$this->photos)),
         ];
    }
}
