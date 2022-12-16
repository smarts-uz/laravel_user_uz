<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property $id
 * @property $reviewer
 * @property $task
 * @property $description
 * @property $good_bad
 * @property $created_at
 */
class ReviewIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    #[ArrayShape([])]
    public function toArray($request): array
    {
        $user = $this->reviewer;
        $task = $this->task;
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        if ($user->last_seen >= $date) {
            $lastSeen = __('В сети');
        } else {
            $seenDate = Carbon::parse($this->last_seen);
            $seenDate->locale(app()->getLocale() . '-' . app()->getLocale());
            if(app()->getLocale()==='uz'){
                $lastSeen = $seenDate->diffForHumans().' saytda edi';
            }else{
                $lastSeen = __('Был онлайн'). $seenDate->diffForHumans();
            }
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
