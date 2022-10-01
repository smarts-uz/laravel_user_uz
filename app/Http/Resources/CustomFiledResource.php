<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFiledResource extends JsonResource
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
            'label' => $this->label,
            'placeholder' => $this->description,
            'type' => $this->type,
            'name' => $this->name,
            'options' => $this->options['options'],
            'order' => $this->order,

        ];
    }
}
