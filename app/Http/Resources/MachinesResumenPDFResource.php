<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachinesResumenPDFResource extends JsonResource
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
			"serie_number" => $this->serie_number,
			"name" => $this->name,
			"brand" => $this->brand,
			"model" => $this->model,
			"maintenance_count" => $this->maintenance_sheets->count(),
			"amount" => $this->get_amount()
		];
	}

	function get_amount()
	{
		return $this->maintenance_sheets->sum(function ($sheet) {
			return $sheet->maintenance_sheet_details->sum(function ($detail) {
				return ($detail->price * $detail->quantity);
			});
		});
	}
}
