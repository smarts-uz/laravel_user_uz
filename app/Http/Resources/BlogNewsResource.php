<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogNewsResource extends JsonResource
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
            'title' => $this->getTranslatedAttribute('title'),
            'text' => $this->getTranslatedAttribute('text'),
            'desc' => $this->getTranslatedAttribute('desc'),
            'img' => asset('storage/'. $this->img),
            'created_at' => $this->created_at
        ];
    }
}
