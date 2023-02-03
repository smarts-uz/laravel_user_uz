<?php

namespace App\Http\Resources;

use App\Models\Task;
use App\Models\TaskResponse;
use App\Services\Task\CustomFieldService;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $photos = array_map(function ($val) {
            return asset('storage/uploads/' . $val);
        },
            json_decode($this->photos) ?? []
        );
        $user_response = TaskResponse::query()
            ->where('task_id', $this->id)
            ->where('performer_id', \auth()->guard('api')->id())
            ->first();
        $performer_response = TaskResponse::query()
            ->where('task_id', $this->id)
            ->where('performer_id', $this->performer_id)
            ->first();
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
            'performer' => new PerformerResponseResource($performer_response),
            'other'=> $this->category->name === "Что-то другое" || $this->category->name === "Boshqa narsa",
            'parent_category_name'=>$this->category->parent->getTranslatedAttribute('name', app()->getLocale(), 'ru'),
            'category_name' => $this->category->getTranslatedAttribute('name', app()->getLocale(), 'ru'),
            'category_id' => $this->category_id,
            'current_user_response' => (bool)$user_response,
            'responses_count' => $this->responses()->count(),
            'user' => new UserInTaskResource($this->user),
            'views' => $this->views,
            'status' => $this->status,
            'oplata' => $this->oplata,
            'docs' => $this->docs,
            'created_at' => $this->created,
            'custom_fields' => (new CustomFieldService())->getCustomFieldsByRoute($this->id, 'custom'),
            'photos' => $photos,
            'performer_review' => $this->performer_review,
            'response_price' => setting('admin.pullik_otklik'),
            'free_response' => setting('admin.bepul_otklik')
        ];
    }
}
