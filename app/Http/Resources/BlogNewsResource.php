<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogNewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
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
