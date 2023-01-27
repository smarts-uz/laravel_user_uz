<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->getTranslatedAttribute('name'),
            'child_count' => $this->childs()->count(),
            'ico' => asset('storage/' . lcfirst($this->ico)),
        ];
    }
}
