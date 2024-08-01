<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceSheetPDFResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
//		dd($this->get_total_absences()->jsonSerialize());
		return [
			"lastname" => $this->lastname,
			"name" => $this->name,
			"attendances" => $this->get_attendances(),
			"absences" => $this->get_absences(),
			"justified_absences" => $this->get_justified_absences(),
			"unexcused_absences" => $this->get_unexcused_absences(),
			"get_total_absences" => $this->get_total_absences(),
			"working_hours" => $this->get_working_hours_total(),
		];
	}

	function get_attendances()
	{
//		dd($this->name);
		return $this->attendance_sheets->where('pivot.attendance', 1)->count();
	}

	/**
	 * @return mixed
	 */
	function get_absences()
	{
		return $this->get_justified_absences() + $this->get_unexcused_absences();
	}

	function get_justified_absences()
	{
		return $this->attendance_sheets->where('pivot.attendance', 0)->whereNotNull('pivot.missed_reason')->count();
	}
	function get_unexcused_absences()
	{
		return $this->attendance_sheets->where('pivot.attendance', 0)->whereNull('pivot.missed_reason')->count();
	}

	function get_total_absences()
	{
		return $this->attendance_sheets->where('pivot.attendance', 0)->sortByDesc('date')->values();
	}

	function get_working_hours_total()
	{
		$dteDiff = 0;
		$sheets = $this->attendance_sheets->where('pivot.attendance', 1);
		$sheets->map(function ($sheet) use (&$dteDiff) {
			$datetime1 = date_create($sheet->pivot->check_in);
			$datetime2 = date_create($sheet->pivot->check_out);
			$interval = date_diff($datetime2, $datetime1);
			$seconds = (($interval->days * 24) * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
			$dteDiff += $seconds;
		});
		return ($this->conversorSegundosHoras($dteDiff));
	}

	function conversorSegundosHoras($tiempo_en_segundos)
	{
		$horas = floor($tiempo_en_segundos / 3600);
		$minutos = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
		$segundos = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);
		$data = [
			'hours' => strlen($horas) > 1 ? $horas : "0" . $horas,
			'minutes' => strlen($minutos) > 1 ? $minutos : "0" . $minutos,
			'secons' => strlen($segundos) > 1 ? $segundos : "0" . $segundos,
		];
		return $data["hours"] . ":" . $data["minutes"] . ":" . $data["secons"];
	}
}
