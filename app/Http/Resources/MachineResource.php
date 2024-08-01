<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class MachineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
//        dd($this->working_sheets);
        return [
            'id' => $this->id,
            'serie_number' => $this->serie_number,
            'name' => $this->name,
            'brand' => $this->brand,
            'model' => $this->model,
            'image' => $this->image ? $this->image->path : null,
            'technical_sheet' => $this->technical_sheet ? $this->technical_sheet->path : null,
            'maximum_working_time' => $this->maximum_working_time,
            'maximum_working_time_per_day' => $this->maximum_working_time_per_day,
						'recommendation' => $this->recommendation,
            'status' => $this->status,
        ];
    }
}
