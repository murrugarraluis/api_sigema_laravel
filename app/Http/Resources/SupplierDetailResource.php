<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
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
            'banks' => SupplierBankResource::collection($this->banks)
        ];
    }
}
