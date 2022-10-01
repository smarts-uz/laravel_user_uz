<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionHistoryResource extends ResourceCollection
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
            'data' => TransactionHistoryCollection::collection($this->collection),
            'total' => $this->total(),
            'first_page_url' => $this->url(1),
            'last_page' => $this->lastPage(),
            'last_page_url' => $this->url($this->lastPage()),
            'count' => $this->count(),
            'test' => $this->hasMorePages(),
            'per_page' => $this->perPage(),
            'links' => $this->getUrlRange(1, $this->lastPage()),
            "prev_page_url" => $this->previousPageUrl(),
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage()
        ];
    }
}
