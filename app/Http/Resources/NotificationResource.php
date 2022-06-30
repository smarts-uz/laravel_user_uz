<?php

namespace App\Http\Resources;

use App\Models\Notification;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{

    protected function titles($type): string
    {
        return match ($type) {
            Notification::TASK_CREATED => __('Новое задание'),
            Notification::NEWS_NOTIFICATION, Notification::SYSTEM_NOTIFICATION => __('Новости'),
            Notification::GIVE_TASK => __('Предложение'),
            Notification::RESPONSE_TO_TASK => __('Отклик к заданию'),
            Notification::SEND_REVIEW => __('Задание выполнено'),
            Notification::SELECT_PERFORMER => __('Вас выбрали исполнителем'),
            Notification::SEND_REVIEW_PERFORMER => __('Новый отзыв'),
            Notification::RESPONSE_TO_TASK_FOR_USER => __('Новый отклик'),
            Notification::CANCELLED_TASK => __('3адания отменен'),
            default => 'Title',
        };
    }

    protected function descriptions($type) {
        return match ($type) {
            Notification::TASK_CREATED => __('task_name  №task_id с бюджетом до task_budget', [
                'task_name' => $this->name_task, 'task_id' => $this->task_id,
                'budget' => number_format($this->task?->budget, 0, '.', ' ')]),
            Notification::NEWS_NOTIFICATION, Notification::SYSTEM_NOTIFICATION => __('Важные новости и объявления для вас'),
            Notification::GIVE_TASK => __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                'task_name' => $this->name_task, 'task_id' => $this->task_id, 'task_user' => $this->user?->name
            ]),
            Notification::RESPONSE_TO_TASK => __('task_name №task_id отправлен', ['task_name' => $this->name_task, 'task_id' => $this->task_id]),
            Notification::SEND_REVIEW => __('Заказчик сказал, что вы выполнили эго задачу task_name №task_id и оставил вам отзыв', [
                'task_name' => $this->name_task, 'task_id' => $this->task_id,
            ]),
            Notification::SELECT_PERFORMER => __('Вас выбрали исполнителем  в задании task_name №task_id task_user', [
                'task_name' => $this->name_task, 'task_id' => $this->task_id, 'task_user' => $this->user?->name]),
            Notification::SEND_REVIEW_PERFORMER => __('О вас оставлен новый отзыв') . " \"$this->name_task\" №$this->task_id",
            Notification::RESPONSE_TO_TASK_FOR_USER => __('performer откликнулся на задания task_name', [
                'performer' => $this->performer?->name, 'task_name' => $this->name_task
            ]),
            Notification::CANCELLED_TASK => __('Ваша задания task_name №task_id было отменена', [
                'task_name' => $this->name_task, 'task_id' => $this->task_id,
            ]),
            default => 'Title',
        };
    }

    /**
     * Transform the resource into an array.
     *
     * @param  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->titles($this->type),
            'description' => $this->descriptions($this->type),
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
