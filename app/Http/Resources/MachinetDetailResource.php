<?php

namespace App\Http\Resources;

use App\Models\WorkingSheet;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class MachinetDetailResource extends JsonResource
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
			'serie_number' => $this->serie_number,
			'name' => $this->name,
			'brand' => $this->brand,
			'model' => $this->model,
			'image' => $this->image ? $this->image->path : null,
			'technical_sheet' => $this->technical_sheet ? $this->technical_sheet->path : null,
			'maximum_working_time' => $this->maximum_working_time,
			'maximum_working_time_per_day' => $this->maximum_working_time_per_day,
			'recommendation' => $this->recommendation,
			'articles' => ArticleResource::collection($this->articles),
//			'articles' => ($this->articles),
			'status' => $this->status,
			'date_last_use' => $this->get_date_last_use(),
			'date_last_maintenance' => $this->get_date_last_maintenance(),
			'total_time_used' => $this->get_total_time_used(),
			'total_time_used_today' => $this->get_total_time_used_today(),

		];
	}

	function get_date_last_use()
	{
		$date_last_use = $this->working_sheets->sortByDesc('date')->first();
		return $date_last_use ? date('Y-m-d', strtotime($date_last_use->date)) : null;
	}

	function get_date_last_maintenance()
	{
		$date_last_maintenance = $this->maintenance_sheets->sortByDesc('date')->first();
		return $date_last_maintenance ? date('Y-m-d', strtotime($date_last_maintenance->date)) : null;
	}

	function get_total_time_used()
	{
		$addtional_second = 0;
		$date_last_maintenance = $this->maintenance_sheets->sortByDesc('date')->first();
		$date_last_maintenance = $date_last_maintenance ? date('Y-m-d H:i:s', strtotime($date_last_maintenance->date)) : null;
		if ($date_last_maintenance) {
			$this->working_sheets
				->where('date', '>=', $date_last_maintenance)
				->map(function ($ws) use (&$addtional_second) {
					$a = $this->get_working_hours_total($ws->working_hours);
					$addtional_second += $a;
				});
		} else {
			$this->working_sheets->map(function ($ws) use (&$addtional_second) {
				$a = $this->get_working_hours_total($ws->working_hours);
				$addtional_second += $a;
			});
		}
		[$hours, $minutes, $seconds] = $this->converterSecondsInTime($addtional_second);
		return [
			'hours' => $hours,
			'minutes' => $minutes,
			'seconds' => $seconds,
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
			$seconds = (($interval->days * 24) * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
			$dteDiff += $seconds;
		}, $working_hours->jsonSerialize());
		return $dteDiff;
//		return $this->conversorSegundosHoras($dteDiff);
	}

	function get_total_time_used_today()
	{
		$addtional_second = 0;
		$today = date('Y-m-d');
		$date_last_maintenance = $this->maintenance_sheets->sortByDesc('date')->first();
		$date_last_maintenance = $date_last_maintenance ? date('Y-m-d H:i:s', strtotime($date_last_maintenance->date)) : null;
		if ($date_last_maintenance) {
			$this->working_sheets->where('date', '>=', $date_last_maintenance)->where('date', '>=', $today)->map(function ($ws) use (&$addtional_second) {
				$a = $this->get_working_hours_total($ws->working_hours);
				$addtional_second += $a;
			});
		} else {
			$this->working_sheets->where('date', '>=', $today)->map(function ($ws) use (&$addtional_second) {
				$a = $this->get_working_hours_total($ws->working_hours);
				$addtional_second += $a;
			});
		}
		[$hours, $minutes, $seconds] = $this->converterSecondsInTime($addtional_second);
		return [
			'hours' => $hours,
			'minutes' => $minutes,
			'seconds' => $seconds,
		];
	}

	function converterSecondsInTime($time_in_seconds)
	{
		$horas = strval(floor($time_in_seconds / 3600));
		$minutos = strval(floor(($time_in_seconds - ($horas * 3600)) / 60));
		$segundos = strval($time_in_seconds - ($horas * 3600) - ($minutos * 60));
//			dump($minutos);
		$data = [
			'hours' => strlen($horas) > 1 ? $horas : "0" . $horas,
			'minutes' => strlen($minutos) > 1 ? $minutos : "0" . $minutos,
			'secons' => strlen($segundos) > 1 ? $segundos : "0" . $segundos,
		];
		return [$data["hours"], $data["minutes"], $data["secons"]];
	}
}
