<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceSheetDetailResource extends JsonResource
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
            'code' => $this->code,
            'date' => $this->date,
            'responsible' => $this->responsible,
            'technical' => $this->technical,
            'description' => $this->description,
            'maintenance_type' => new MaintenanceTypeResource($this->maintenance_type),
            'supplier' => new SupplierResource($this->supplier),
            'machine' => new MachinetDetailResource($this->machine),
            'ref_invoice_number' => $this->ref_invoice_number,
            "maximum_working_time" => $this->maximum_working_time,
            'detail' => MaintenanceSheetArticlesResource::collection($this->maintenance_sheet_details->sortBy('item')),
            'recommendation' => $this->machine->recommendation,
						'amount' => $this->maintenance_sheet_details->sum(function ($detail) {
                return ($detail->price * $detail->quantity);
            })
        ];
    }
}
