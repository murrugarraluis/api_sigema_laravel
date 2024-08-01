<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkingSheetDetailResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$working_hours = WorkingHourResource::collection($this->working_hours)->sortByDesc('created_at');
		return [
			'id' => $this->id,
			'code' => $this->code,
			'date' => $this->date,
			'description' => $this->description,
			'machine' => new MachinetDetailResource($this->machine),
			'working_hours' => $working_hours->toArray(),
			'working_hours_total' => $this->get_working_hours_total($working_hours),
			'is_open' => $this->is_open,
			'is_pause' => ($this->working_hours()->orderBy('created_at', 'desc')->first()->date_time_end && $this->is_open)
		];
	}

	function get_working_hours_total($working_hours)
	{
//        $dteDiff = date_create("0000-00-00 00:00:00");
		$dteDiff = 0;
		array_map(function ($item) use (&$dteDiff) {
			$datetime1 = date_create($item["date_time_start"]);
			$datetime2 = date_create($item["date_time_end"]);
			$interval = date_diff($datetime2, $datetime1);
			$seconds = (($interval->days * 24) * 60 * 60) +($interval->h * 60 * 60)+  ($interval->i * 60) + $interval->s;
			$dteDiff += $seconds;
		}, $working_hours->jsonSerialize());
		return $this->conversorSegundosHoras($dteDiff);
	}

	function conversorSegundosHoras($tiempo_en_segundos)
	{
		$horas = strval(floor($tiempo_en_segundos / 3600));
		$minutos = strval(floor(($tiempo_en_segundos - ($horas * 3600)) / 60));
		$segundos = strval($tiempo_en_segundos - ($horas * 3600) - ($minutos * 60));
		$data = [
			'hours' => strlen($horas) > 1 ? $horas : "0" . $horas,
			'minutes' => strlen($minutos) > 1 ? $minutos : "0" . $minutos,
			'secons' => strlen($segundos) > 1 ? $segundos : "0" . $segundos,
		];
		return $data["hours"] . ":" . $data["minutes"] . ":" . $data["secons"];
	}
}
