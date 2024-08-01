<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceSheetDetailReportResource extends JsonResource
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
			'code' => $this->code,
			'date' => $this->date,
			'responsible' => $this->responsible,
			'maintenance_type' => ["name" => $this->maintenance_type->name],
			'supplier' => ["name" => $this->supplier->name],
		];
	}
}
