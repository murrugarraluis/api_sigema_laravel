<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierBankResource extends JsonResource
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
            'id'=>$this->id,
            'name'=>$this->name,
            'account_number'=>$this->pivot->account_number,
            'interbank_account_number'=>$this->pivot->interbank_account_number,
        ];
    }
}
