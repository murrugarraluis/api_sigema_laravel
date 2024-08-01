<?php

namespace App\Http\Resources;

use App\Http\Controllers\SupplierTypeController;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class SupplierResource extends JsonResource
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
            'document_number' => $this->document_number,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'supplier_type' => new SupplierTypeResource($this->supplier_type),
            'document_type' => new DocumentTypeResource($this->document_type),
        ];
    }
}
