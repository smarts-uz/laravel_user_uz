<?php

namespace App\Http\Resources;

use App\Services\NotificationService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property $id
 * @property $type
 * @property $is_read
 * @property $user_id
 * @property $task_id
 * @property $name_task
 * @property $user
 * @property $performer
 * @property $task
 * @property $created_at
 *
 */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => NotificationService::titles($this->type),
            'description' => NotificationService::descriptions($this),
            'type' => $this->type,
            'task_id' => $this->task_id,
            'task_name' => $this->name_task,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name ?? null,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at?->format('d.m.Y')
        ];
    }
}
