<?php

namespace App\Http\Resources;

use App\Models\MaintenanceType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceSheetResource extends JsonResource
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
            'code' => $this->code,
            'date' => $this->date,
            'responsible' => $this->responsible,
            'technical' => $this->technical,
            'description' => $this->description,
            'maintenance_type' => new MaintenanceTypeResource($this->maintenance_type),
            'supplier' => new SupplierResource($this->supplier),
            'machine' => new MachineResource($this->machine),
            'ref_invoice_number' => $this->ref_invoice_number,
            "maximum_working_time" => $this->maximum_working_time,
            "amount" => $this->get_amount(),
        ];
    }
		function get_amount(){
			return $this->maintenance_sheet_details->sum(function ($detail) {
				return ($detail->price * $detail->quantity);
			});
		}
}
