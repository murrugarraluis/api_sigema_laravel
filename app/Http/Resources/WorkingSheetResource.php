<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class WorkingSheetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'code' => $this->code,
//            'date_end' => $this->date_end,
            'description' => $this->description,
            'machine' => new MachineResource($this->machine),
            'is_open' => $this->is_open

        ];
    }
}
