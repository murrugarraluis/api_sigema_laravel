<?php

namespace App\Models;

use App\Traits\Uuids;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Attribute;

class Machine extends Model
{
	use HasFactory, Uuids, SoftDeletes;

	protected $fillable = [
		'serie_number',
		'name',
		'brand',
		'model',
		'image',
		'maximum_working_time',
		'maximum_working_time_per_day',
		'recommendation'
	];
	protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
	protected $with = ['image', 'working_sheets', 'working_sheets.working_hours', 'maintenance_sheets', 'technical_sheet','articles'];

	public function articles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Article::class)->withTrashed();
	}

	public function image(): \Illuminate\Database\Eloquent\Relations\MorphOne
	{
		return $this->morphOne(Image::class, 'imageable');
	}

	public function technical_sheet(): \Illuminate\Database\Eloquent\Relations\MorphOne
	{
		return $this->morphOne(TechnicalSheet::class, 'technical_sheetable');
	}

	public function maintenance_sheets(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(MaintenanceSheet::class);
	}

	public function working_sheets(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(WorkingSheet::class);
	}

	public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(Notification::class);
	}


	private function converterHourInSeconds($hour)
	{
		return $hour * 3600;
	}

	public function getStatusAttribute(): string
	{
		$status = ["available", "operating", "unavailable"];

		$time_working_today = $this->getTimeWorkingToday();
		$time_working = $this->getTimeWorking();
		if (
			$time_working_today + $this->converterHourInSeconds(1) >= $this->converterHourInSeconds($this->maximum_working_time_per_day) ||
			$time_working + $this->converterHourInSeconds(6) >= $this->converterHourInSeconds($this->maximum_working_time)
		) {
			return $status[2];
		}

		$is_working = $this->getIsWorking();
		if ($is_working) {
			return $status[1];
		}

		return $status[0];
	}

	private function getTimeWorkingToday()
	{
		$sum_working_hours_in_seconds = $this->working_sheets
			->where('date', '>=', date('Y-m-d'))
			->sum(function ($ws) {
				return $ws->working_hours->sum(function ($wh) {
					$datetime1 = date_create($wh["date_time_start"]);
					$datetime2 = date_create($wh["date_time_end"]);
					$interval = date_diff($datetime2, $datetime1);
					$seconds = (($interval->days * 24) * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
					return $seconds;
				});
			});
		return $sum_working_hours_in_seconds;
	}

	private function getTimeWorking()
	{
		$date_last_maintenance = $this->maintenance_sheets->sortByDesc('date')->first();
		$date_last_maintenance = $date_last_maintenance ? date('Y-m-d H:i:s', strtotime($date_last_maintenance->date)) : null;
		if ($date_last_maintenance) {
			$sum_working_hours_in_seconds = $this->working_sheets
				->where('date', '>=', $date_last_maintenance)
				->sum(function ($ws) {
					return $ws->working_hours->sum(function ($wh) {
						$datetime1 = date_create($wh["date_time_start"]);
						$datetime2 = date_create($wh["date_time_end"]);
						$interval = date_diff($datetime2, $datetime1);
						$seconds = (($interval->days * 24) * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
						return $seconds;
					});
				});
		} else {
			$sum_working_hours_in_seconds = $this->working_sheets
				->sum(function ($ws) {
					return $ws->working_hours->sum(function ($wh) {
						$datetime1 = date_create($wh["date_time_start"]);
						$datetime2 = $wh["date_time_end"] ? date_create($wh["date_time_end"]) : new DateTime(); // Usar la hora actual si es null
						$interval = date_diff($datetime2, $datetime1);
						$seconds = (($interval->days * 24) * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
						return $seconds;
					});
				});

		}
		return $sum_working_hours_in_seconds;
	}

	private function getIsWorking(): bool
	{
		return ($this->working_sheets->where('is_open', true)->count() > 0);
	}

	function get_date_last_maintenance()
	{
		$date_last_maintenance = $this->maintenance_sheets()->orderBy('date', 'desc')->first();
		return $date_last_maintenance ? date('Y-m-d', strtotime($date_last_maintenance->date)) : null;
	}

	function getAmountAttribute()
	{
		return $this->maintenance_sheets->sum(function ($sheet) {
			return $sheet->maintenance_sheet_details->sum(function ($detail) {
				return ($detail->price * $detail->quantity);
			});
		});
	}
//    function getmaintenanceCountAttribute()
//    {
//        return $this->maintenance_sheets()->count();
//    }
//
//    protected function amountTotal(): Attribute
//    {
//        return Attribute::make(
//            get: fn($value) => ucfirst("Hola"),
//        );
//    }
}
