<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceSheetArticlesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        dd($this);
        return [
            'id' => $this->id,
            'article' => $this->article ? new ArticleResource($this->article) : null,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'item' => $this->item,
        ];
    }
}
