<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioIndexResource extends JsonResource
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
            'user_id' => $this->user_id,
            'comment' => $this->comment,
            'description' => $this->description,
            'images' => $this->makeAssets(json_decode($this->image??"[]")),
        ];
    }

    public function makeAssets($collection){
        $arr = [];
        foreach ($collection as $item) {
            $arr[] = asset('Portfolio/'.$item);
        }
        return $arr;
    }


}
