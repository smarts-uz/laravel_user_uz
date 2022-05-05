<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{

    protected function titles($type, $title) {
        switch ($type) {
            case 1:
                return 'Новая задания';
            case 3:
            case 2:
                return $title;
            case 4:
                return 'Новая задания для вас';
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

    protected function descriptions($type, $description) {
        switch ($type) {
            case 1:
                return 'Новая задания';
            case 3:
            case 2:
                return $description;
            case 4:
                return 'Новая задания для вас';
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
            'title' => $this->name_task,//$this->titles($this->type, $this->name_task),
            'type' => $this->type,
            'description' => $this->description, //$this->descriptions($this->type, $this->description)
            'created_at' => $this->created_at->format('d.m.Y')
        ];
    }
}
