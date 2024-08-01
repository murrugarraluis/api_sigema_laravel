<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkingHourResource extends JsonResource
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
			'date_time_start' => $this->date_time_start,
			'date_time_end' => $this->date_time_end,
			'date_time_diff' => $this->date_time_diff(),

		];
	}

	function date_time_diff()
	{
		$datetime1 = date_create($this->date_time_start);
		$datetime2 = date_create($this->date_time_end);
		$interval = date_diff($datetime2, $datetime1);
		$seconds = (($interval->days * 24) * 60 * 60) +($interval->h * 60 * 60)+  ($interval->i * 60) + $interval->s;
		return $this->conversorSegundosHoras($seconds);
	}

	function conversorSegundosHoras($tiempo_en_segundos)
	{
		$horas = strval(floor($tiempo_en_segundos / 3600));
		$minutos = strval(floor(($tiempo_en_segundos - ($horas * 3600)) / 60));
		$segundos = strval($tiempo_en_segundos - ($horas * 3600) - ($minutos * 60));
		return [
			'hours' => strlen($horas) > 1 ? $horas : "0" . $horas,
			'minutes' => strlen($minutos) > 1 ? $minutos : "0" . $minutos,
			'secons' => strlen($segundos) > 1 ? $segundos : "0" . $segundos,
		];
	}
}
