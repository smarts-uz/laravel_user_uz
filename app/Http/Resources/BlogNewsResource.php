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
        $img = (!empty($this->img)) ? asset('storage/'. $this->img) : '';
        return [
            'id' => $this->id,
            'title' => (!empty($this->title)) ? $this->getTranslatedAttribute('title') : '',
            'text' => (!empty($this->text)) ? $this->getTranslatedAttribute('text') : '',
            'desc' => (!empty($this->desc)) ? $this->getTranslatedAttribute('desc') : '',
            'img' => $img,
            'created_at' => (!empty($this->created_at)) ? $this->created_at : ''
        ];
    }
}
