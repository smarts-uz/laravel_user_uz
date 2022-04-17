<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SameTaskResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address? json_decode($this->address): __('udalyonka'),
            'budget' => $this->budget,
            'image' => asset('storage/'.$this->category->ico),
            'oplata' => $this->oplata,
            'start_date' => $this->start_date
        ];
    }
}
