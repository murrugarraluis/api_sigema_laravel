<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceSheetDetailResource extends JsonResource
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
			'date' => $this->date,
//            'time_start' => $this->time_start,
//            'time_end' => $this->time_end,
			'responsible' => $this->responsible,
			'turn' => $this->turn,
			'employees' => AttendanceEmployeeResource::collection($this->employees),
			'is_open' => $this->is_open,
		];
	}
}
