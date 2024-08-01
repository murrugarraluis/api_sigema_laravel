<?php

namespace App\Http\Resources;

use App\Models\WorkingSheet;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class WorkingSheetMachineResource extends JsonResource
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
            'serie_number' => $this->serie_number,
            'name' => $this->name,
            'brand' => $this->brand,
            'model' => $this->model,
            'image' => $this->image ? $this->image->path : null,
            'maximum_working_time' => $this->maximum_working_time,
            'status' => $this->status,

            'date_last_use' => $this->get_date_last_use(),
            'date_last_maintenance' => $this->get_date_last_maintenance(),
            'total_time_used' => $this->get_total_time_used(),
        ];
    }

    function get_date_last_use()
    {
        $date_last_use = $this->working_sheets()->orderBy('date', 'desc')->first();
        return $date_last_use ? date('Y-m-d', strtotime($date_last_use->date)) : null;
    }

    function get_date_last_maintenance()
    {
        $date_last_maintenance = $this->maintenance_sheets()->orderBy('date', 'desc')->first();
        return $date_last_maintenance ? date('Y-m-d', strtotime($date_last_maintenance->date)) : null;
    }

    function get_total_time_used()
    {
//        $date_last_maintenance = $this->get_date_last_maintenance();
        $date_last_maintenance = $this->maintenance_sheets()->orderBy('date', 'desc')->first();
        $date_last_maintenance = $date_last_maintenance ? date('Y-m-d H:i:s', strtotime($date_last_maintenance->date)) : null;
//        dd($date_last_maintenance);
        if ($date_last_maintenance) {
            $sum_working_hours_in_seconds = WorkingSheet::join(DB::raw('(SELECT working_sheet_id,
                            SUM(TIMESTAMPDIFF(SECOND, date_time_start, date_time_end)) AS total_seconds
                     from working_hours
                     GROUP BY working_sheet_id) AS wh '),
                function ($join) {
                    $join->on('wh.working_sheet_id', '=', 'working_sheets.id');
                })
                ->where('machine_id', $this->id)
                ->where('date', '>=', $date_last_maintenance)
//                ->get();
                ->sum('total_seconds');
//            dd($sum_working_hours_in_seconds);
        } else {
            $sum_working_hours_in_seconds = WorkingSheet::join(DB::raw('(SELECT working_sheet_id,
                            SUM(TIMESTAMPDIFF(SECOND, date_time_start, date_time_end)) AS total_seconds
                     from working_hours
                     GROUP BY working_sheet_id) AS wh '),
                function ($join) {
                    $join->on('wh.working_sheet_id', '=', 'working_sheets.id');
                })
                ->where('machine_id', $this->id)
                ->sum('total_seconds');
        }
//        return $sum_working_hours_in_seconds;

        [$hours, $minutes, $seconds] = $this->converterSecondsInTime($sum_working_hours_in_seconds);
        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
        ];
    }

    function converterSecondsInTime($time_in_seconds)
    {
        $hours = strval(floor($time_in_seconds / 3600));
        $minutes = strval(floor(($time_in_seconds - ($hours * 3600)) / 60));
        $seconds = strval($time_in_seconds - ($hours * 3600) - ($minutes * 60));

        return [$hours, $minutes, $seconds];
    }
}
