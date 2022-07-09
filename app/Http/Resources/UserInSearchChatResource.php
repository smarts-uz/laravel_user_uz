<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInSearchChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active_status' => $this->active_status,
            'avatar' => url('/storage') . '/' . $this->avatar,
            'last_seen' => $this->last_seen
        ];
    }
}
