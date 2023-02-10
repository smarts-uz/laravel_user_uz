<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
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
            $arr[] = asset('/storage/portfolio/'.$item);
        }
        return $arr;
    }


}
