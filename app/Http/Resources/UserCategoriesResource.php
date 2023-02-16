<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $category
 */
class UserCategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->category->getTranslatedAttribute('name'),
            'task_count' => $this->category->tasks()->where('performer_id',auth()->user()->id)->where('status',Task::STATUS_COMPLETE)->count(),
        ];
    }
}
