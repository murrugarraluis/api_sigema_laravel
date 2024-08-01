<?php

namespace App\Http\Resources;

use App\Models\Article;
use App\Models\AttendanceSheet;
use App\Models\Employee;
use App\Models\Machine;
use App\Models\MaintenanceSheet;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WorkingSheet;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
//		dd($this[0]);
		return [
			"machines" => [
				"count" => Machine::all()->count(),
			],
			"working_sheets" => [
				"count_today" => WorkingSheet::whereDate('date', date('Y-m-d'))->get()->count(),
			],
			"maintenance_sheets" => [
				"count_today" => MaintenanceSheet::whereDate('date', date('Y-m-d'),)->get()->count(),
			],
			"employees" => [
				"count" => Employee::all()->count(),
			],
			"attendance_sheets" => [
				"count_today" => AttendanceSheet::whereDate('date', date('Y-m-d'),)->get()->count(),
			],
			"articles" => [
				"count" => Article::all()->count(),
			],
			"suppliers" => [
				"count" => Supplier::all()->count(),
			],
			"users" => [
				"count" => User::all()->count(),
			]
		];
	}
}
