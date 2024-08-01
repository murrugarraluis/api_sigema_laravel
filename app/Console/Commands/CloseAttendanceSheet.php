<?php

namespace App\Console\Commands;

use App\Models\AttendanceSheet;
use App\Models\Configuration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CloseAttendanceSheet extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'close:attendance-sheet';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		try {
			$time = date('H:i:s');
			$search = "end_time_turn";
			$times = Configuration::where('name', 'like', '%' . $search . '%')->get();
			$end_time_db_day = $times->where('name', $search . '_day')->first()->value;
			$end_time_db_night = $times->where('name', $search . '_night')->first()->value;

			if ($time == $end_time_db_day) {
//				CERRAR LISTA DE HOY TURNO MAÑANA
				$today = date('Y-m-d');
				$attendanceSheet = AttendanceSheet::whereDate('date', $today)->where('turn', 'day')->first();
				$attendanceSheet->update(['is_open' => false]);
				$this->updateEmployees($attendanceSheet, $end_time_db_day);

			}
			if ($time == $end_time_db_night) {
//				CERRAR LISTA DE AYER TURNO NOCHE
				$yesterday = date('Y-m-d', strtotime('-1 day'));
				$attendanceSheet = AttendanceSheet::whereDate('date', $yesterday)->where('turn', 'night')->first();
				$attendanceSheet->update(['is_open' => false]);
				$this->updateEmployees($attendanceSheet, $end_time_db_night);
			}
		} catch (\Exception $e) {
			Storage::append("CloseAttendanceSheet.txt", $e);
		} finally {
			return 0;
		}
	}

	function updateEmployees(AttendanceSheet $attendanceSheet, $end_time_db)
	{
		$employees = [];
		$attendanceSheet->employees->map(function ($employee) use (&$employees, $end_time_db) {
			$employee_id = $employee['id'];
//			dd($employee['pivot']);
			$check_in = $employee['pivot']['check_in'];
			$check_out = $employee['pivot']['check_out'];
			$attendance = $employee['pivot']['attendance'];
			$missed_reason = $employee['pivot']['missed_reason'];
			$missed_description = $employee['pivot']['missed_description'];
			if ($attendance && $check_in && !$check_out) {
				$employees[$employee_id] = [
					"check_out" => $end_time_db
				];
			} else {
				$employees[$employee_id] = [
					"check_in" => $check_in,
					"check_out" => $check_out,
					"attendance" => $attendance,
					"missed_reason" => $missed_reason,
					"missed_description" => $missed_description,
				];
			}
		});
		$attendanceSheet->employees()->sync($employees);
	}
}
