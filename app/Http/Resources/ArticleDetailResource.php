<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'serie_number' => $this->serie_number,
            'name' => $this->name,
            'brand' => $this->brand,
            'model' => $this->model,
            'quantity' => $this->quantity,
            'article_type' => $this->article_type,
            'image' => $this->image ? $this->image->path : null,
            'technical_sheet' => $this->technical_sheet ? $this->technical_sheet->path : null,
            'suppliers' => ArticleSupplierResource::collection($this->suppliers)
        ];
    }
}
