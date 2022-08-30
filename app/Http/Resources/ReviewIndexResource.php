<?php

namespace App\Http\Resources;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this->user;
        $task = $this->task;
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        if ($user->last_seen >= $date) {
            $lastSeen = 'online';
        } else {
            $lastSeen = $user->last_seen_at;
        }
        return [
            'id' => $this->id,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'last_seen' => $lastSeen,
                'review_good' => $user->review_good,
                'review_bad' => $user->review_bad,
                'rating' => $user->review_rating,
                'avatar' => url('/storage') . '/' . $user->avatar,
            ],
            'description' => $this->description,
            'good_bad' => $this->good_bad,
            'task' => [
                'name' => $task->name,
                'description' => $task->description
            ],
            'created_at' => $this->created_at
        ];
    }
}
