<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{

    protected function titles($type) {
        switch ($type) {
            case 1:
                return 'Создана новая задача';
            case 3:
            case 2:
                return 'Новости о сайте';
            case 4:
                return 'Заказчик предложил вам новую заданию';
            case 5:
                return 'Отклик задания';
            case 6:
                return 'Задача была оценена';
            case 7:
                return 'Вас выбрали';
            default:
                return 'Title';
        }
    }

    protected function descriptions($type, $notification) {
        switch ($type) {
            case 1:
                return 'Новая задания ' . $notification->name_task . ' №'. $notification->task_id;
            case 3:
            case 2:
                return $notification->description;
            case 4:
                return 'Заказчик предложил вам новую заданию ' . $notification->name_task . '№' . $notification->task_id;
            case 5:
                return 'Новый отклик';
            case 6:
                return 'Новый отзыв';
            case 7:
                return 'Вас выбрали';
            default:
                return 'Title';
        }
    }

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
            'title' => $this->titles($this->type),
            'type' => $this->type,
            'task_id' => $this->task_id,
            'task_name' => $this->name_task,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name ?? null,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at->format('d.m.Y')
        ];
    }
}
