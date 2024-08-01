<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendancePDFRequest;
use App\Http\Requests\AttendanceStoreRequest;
use App\Http\Requests\AttendanceUpdateCheckInRequest;
use App\Http\Requests\AttendanceUpdateCheckOutRequest;
use App\Http\Requests\AttendanceUpdateJustifiedAbsenceRequest;
use App\Http\Requests\AttendanceUpdateRequest;
use App\Http\Resources\AttendanceSheetDetailResource;
use App\Http\Resources\AttendanceSheetPDFResource;
use App\Http\Resources\AttendanceSheetResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\MachinesDetailPDFResource;
use App\Http\Resources\MachinesResumenPDFResource;
use App\Models\AttendanceSheet;
use App\Models\Configuration;
use App\Models\Employee;
use App\Models\Machine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AttendanceSheetController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return AnonymousResourceCollection
	 */
	public function index(Request $request): AnonymousResourceCollection
	{
		$attendance_sheets = AttendanceSheet::all()->sortByDesc('created_at');
		if ($request->start_date && $request->end_date) {
			//            $attendance_sheets = AttendanceSheet::whereDateBetween('date', [$request->start_date, $request->end_date])
			//                ->get()->sortByDesc('created_at');
			$attendance_sheets = AttendanceSheet::whereDate('date', '>=', $request->start_date)->whereDate('date', '<=', $request->end_date)
				->get()->sortByDesc('created_at');
		}
		return AttendanceSheetResource::collection($attendance_sheets);
	}

	public function index_pdf(AttendancePDFRequest $request)
	{
//		$employees = Employee::with('attendance_sheets')->whereHas('attendance_sheets', function (Builder $query) use ($request) {
//			$query->whereDate('attendance_sheets.date', '>=', $request->start_date)
//				->whereDate('attendance_sheets.date', '<=', $request->end_date);
//		})->get();
		$employees = Employee::with(['attendance_sheets' => function ($query) use ($request) {
			$query->whereDate('attendance_sheets.date', '>=', $request->start_date)
				->whereDate('attendance_sheets.date', '<=', $request->end_date);
		}])->get();
//		$users = App\User::with(['posts' => function ($query) {
//			$query->where('title', 'like', '%first%');
//
//		}])->get();
//		return $employees;
		$employees = AttendanceSheetPDFResource::collection($employees);
//		return ($employees);
//		dd($employees->jsonSerialize());


		if ($request->order_by == "asc") {
			$report = $employees->sortBy(function ($product) use ($request) {
				return ($product->jsonSerialize()[$request->sort_by]);
			})->values();
		} else {
			$report = $employees->sortByDesc(function ($product) use ($request) {
				return ($product->jsonSerialize()[$request->sort_by]);
			})->values();
		}
//		return $report;
//
		$data = [
			"title" => $request->type == 'attended' ? 'title_attendance_sheet' : 'title_absences_sheet',
			"type" => $request->type,
			"sort_by" => $request->sort_by,
			"start_date" => $request->start_date,
			"end_date" => $request->end_date,
			"date_report" => date('Y-m-d'),
			"employees" => $report,
			"total_employees" => $employees->count(),
			"total_attendances" => $employees->sum(function ($employee) {
				return ($employee->jsonSerialize()['attendances']);
			}),
			"total_absences" => $employees->sum(function ($employee) {
				return ($employee->jsonSerialize()['absences']);
			}),
			"total_justified_absences" => $employees->sum(function ($employee) {
				return ($employee->jsonSerialize()['justified_absences']);
			}),
			"total_unexcused_absences" => $employees->sum(function ($employee) {
				return ($employee->jsonSerialize()['unexcused_absences']);
			}),
		];
//		return $data;
		$language = (Auth()->user()->employee->native_language);
		$locale = $language == 'spanish' ? 'es' : 'en';
		App::setLocale($locale);


		$pdf = \PDF::loadView('attendance-report', compact('data'));
		$orientation = $request->type == 'attended' ? 'portraint' : 'landscape';
		$pdf->setPaper('A4', $orientation);
//        $font = $pdf->getFontMetrics()->get_font("helvetica", "bold");
//        $pdf->getCanvas()->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0,0,0));


//		 return $pdf->download();


		$name_file = Str::uuid()->toString();
		$path = 'public/reports/' . $name_file . '.pdf';
		Storage::put($path, $pdf->output());
		$path = (substr($path, 7, strlen($path)));

		return [
			'path' => $path
		];

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return JsonResponse|object
	 */
	public
	function store(AttendanceStoreRequest $request)
	{
		DB::beginTransaction();
		try {
			//          CREATE
			$count_attendance_sheet = AttendanceSheet::whereDate('date', date('Y-m-d'))->get()->count();
			if ($count_attendance_sheet > 1) return response()->json(['message' => 'cannot create more than two records per day.'])->setStatusCode(400);
			$attendance_sheet = AttendanceSheet::create([
				'date' => date('Y-m-d H:i:s'),
				'responsible' => Auth()->user()->employee()->first()->name . " " . Auth()->user()->employee()->first()->lastname,
				'turn' => $request->turn,
				'is_open' => true,
			]);

			$employees = [];
			array_map(function ($employee) use (&$employees) {
				$employees[] = $employee['id'];
			}, $request->employees);
			$attendance_sheet->employees()->attach($employees);

			DB::commit();
			return (new AttendanceSheetDetailResource($attendance_sheet))
				->additional(['message' => 'Attendance Sheet created . '])
				->response()
				->setStatusCode(201);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param AttendanceSheet $attendanceSheet
	 * @return AttendanceSheetDetailResource
	 */
	public
	function show(AttendanceSheet $attendanceSheet): AttendanceSheetDetailResource
	{
		return new AttendanceSheetDetailResource($attendanceSheet);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param AttendanceUpdateRequest $request
	 * @param AttendanceSheet $attendanceSheet
	 * @return AttendanceSheetDetailResource
	 */
	public
	function update(AttendanceUpdateRequest $request, AttendanceSheet $attendanceSheet): AttendanceSheetDetailResource
	{
		DB::beginTransaction();
		try {
			if ($request->employees) {
				$employees = [];
				$search = "time_turn_" . $attendanceSheet->turn;
				$times = Configuration::where('name', 'like', '%' . $search . '%')->get();
				$start_time_db = $times->where('name', 'start_' . $search)->first()->value;
				$end_time_db = $times->where('name', 'end_' . $search)->first()->value;

				array_map(function ($employee) use (&$employees, $start_time_db, $end_time_db, $request) {
					$employee_id = $employee['id'];
					$check_in = array_key_exists('check_in', $employee) ? $employee['check_in'] : null;
					$check_out = array_key_exists('check_out', $employee) ? $employee['check_out'] : null;
					$attendance = $employee['attendance'];
					$missed_reason = array_key_exists('missed_reason', $employee) ? $employee['missed_reason'] : null;;
					$missed_description = array_key_exists('missed_description', $employee) ? $employee['missed_description'] : null;;

					if ($request->is_open) {
						$employees[$employee_id] = [
							"check_in" => $check_in <= $start_time_db ? $start_time_db : $check_in,
							"check_out" => $check_out <= $end_time_db ? $check_out : $end_time_db,
							"attendance" => $attendance,
							"missed_reason" => $missed_reason,
							"missed_description" => $missed_description,
						];
					} else {
						if ($attendance && $check_in && !$check_out) {
							$employees[$employee_id] = [
								"check_out" => $check_out <= $end_time_db ? $check_out : $end_time_db,
							];
						} else {
							$employees[$employee_id] = [
								"check_in" => $check_in && $check_in <= $start_time_db ? $start_time_db : $check_in,
								"check_out" => $check_out <= $end_time_db ? $check_out : $end_time_db,
								"attendance" => $attendance,
								"missed_reason" => $missed_reason,
								"missed_description" => $missed_description,
							];
						}
					}

				}, $request->employees);
				$attendanceSheet->employees()->sync($employees);
			}
			if ($request->has('is_open')) {
				$attendanceSheet->update(['is_open' => $request->is_open]);
			}
			DB::commit();
			return (new AttendanceSheetDetailResource($attendanceSheet))
				->additional(['message' => 'Attendance Sheet updated.']);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	function get_config_times($date, $turn)
	{
		$search = "time_turn_" . $turn;
		$times = Configuration::where('name', 'like', '%' . $search . '%')->get();
		$start_time_db = $times->where('name', 'start_' . $search)->first()->value;
		$end_time_db = $times->where('name', 'end_' . $search)->first()->value;

		if ($start_time_db <= $end_time_db) {
			$start_time_db = $date . " " . $start_time_db;
			$end_time_db = $date . " " . $end_time_db;
		} else {
			$start_time_db = $date . " " . $start_time_db;
			$end_time_db = date('Y-m-d', strtotime($date . "+ 1 days")) . " " . $end_time_db;
		}
		return [$start_time_db, $end_time_db];
	}

	function check_in(AttendanceUpdateCheckInRequest $request, AttendanceSheet $attendanceSheet)
	{
		DB::beginTransaction();
		try {
//			$employees = [];
			if (!$attendanceSheet->is_open) return response()->json(['message' => 'cannot modify a closed sheet.'], 400);

			$date_format = date('Y-m-d', strtotime($attendanceSheet->date));
			[$start_time_db, $end_time_db] = $this->get_config_times($date_format, $attendanceSheet->turn);
			array_map(function ($employee) use ($start_time_db, $end_time_db, $request, $attendanceSheet) {
				$employee_id = $employee['id'];
				$check_in = $employee['check_in'];
				$employee_db = $attendanceSheet->employees()->where('id', $employee_id)->first()->pivot;
				if (!$employee_db->check_in && !$employee_db->attendance && !$employee_db->missed_reason) {
					$changes = [
						"check_in" => $start_time_db < $check_in && $check_in < $end_time_db ? $check_in : $start_time_db,
						"attendance" => true,
					];
					$attendanceSheet->employees()->updateExistingPivot($employee_id, $changes);
				}
			}, $request->employees);
			DB::commit();
			return (new AttendanceSheetDetailResource($attendanceSheet->load('employees')))
				->additional(['message' => 'Attendance Sheet updated.']);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	function check_out(AttendanceUpdateCheckOutRequest $request, AttendanceSheet $attendanceSheet)
	{
		DB::beginTransaction();
		try {
//			$employees = [];
			if (!$attendanceSheet->is_open) return response()->json(['message' => 'cannot modify a closed sheet.'], 400);

			$date_format = date('Y-m-d', strtotime($attendanceSheet->date));
			[$start_time_db, $end_time_db] = $this->get_config_times($date_format, $attendanceSheet->turn);
			array_map(function ($employee) use ($start_time_db, $end_time_db, $request, $attendanceSheet) {
				$employee_id = $employee['id'];
				$check_out = $employee['check_out'];
				$employee_db = $attendanceSheet->employees()->where('id', $employee_id)->first()->pivot;
				if (!$employee_db->check_out && $employee_db->attendance && !$employee_db->missed_reason) {
					$changes = [
						"check_out" => $start_time_db < $check_out && $check_out < $end_time_db ? $check_out : $end_time_db,
					];
					$attendanceSheet->employees()->updateExistingPivot($employee_id, $changes);
				}
			}, $request->employees);
			DB::commit();
			return (new AttendanceSheetDetailResource($attendanceSheet->load('employees')))
				->additional(['message' => 'Attendance Sheet updated.']);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	function justified_absence(AttendanceUpdateJustifiedAbsenceRequest $request, AttendanceSheet $attendanceSheet)
	{
		DB::beginTransaction();
		try {
			array_map(function ($employee) use ($request, $attendanceSheet) {
				$employee_id = $employee['id'];
				$employee_db = $attendanceSheet->employees()->where('id', $employee_id)->first()->pivot;
				if (!$employee_db->check_in && !$employee_db->check_out && !$employee_db->missed_reason) {
					$changes = [
						"attendance" => false,
						"missed_reason" => $employee['missed_reason'],
						"missed_description" => $employee['missed_description'],
					];
					$attendanceSheet->employees()->updateExistingPivot($employee_id, $changes);
				}
			}, $request->employees);
			DB::commit();
			return (new AttendanceSheetDetailResource($attendanceSheet->load('employees')))
				->additional(['message' => 'Attendance Sheet updated.']);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	function closed(AttendanceSheet $attendanceSheet)
	{
		DB::beginTransaction();
		try {
			$date_format = date('Y-m-d', strtotime($attendanceSheet->date));
			[$start_time_db, $end_time_db] = $this->get_config_times($date_format, $attendanceSheet->turn);
			$this->updateEmployees($attendanceSheet, $end_time_db);
			$attendanceSheet->update(['is_open' => false]);
			DB::commit();
			return (new AttendanceSheetDetailResource($attendanceSheet->load('employees')))
				->additional(['message' => 'Attendance Sheet updated.']);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	function updateEmployees(AttendanceSheet $attendanceSheet, $end_time_db)
	{
		$employees = [];
		$now = date('Y-m-d H:i:s');
		$attendanceSheet->employees->map(function ($employee) use (&$employees, $end_time_db, $now) {
			$employee_id = $employee['id'];
			$check_in = $employee['pivot']['check_in'];
			$check_out = $employee['pivot']['check_out'];
			$attendance = $employee['pivot']['attendance'];
			$missed_reason = $employee['pivot']['missed_reason'];
			$missed_description = $employee['pivot']['missed_description'];
			if ($attendance && $check_in && !$check_out) {
				$employees[$employee_id] = [
					"check_out" => $now < $end_time_db ? $now : $end_time_db
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

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param AttendanceSheet $attendanceSheet
	 * @return JsonResponse
	 */
	public
	function destroy(AttendanceSheet $attendanceSheet): JsonResponse
	{
		$attendanceSheet->delete();
		return response()->json(['message' => 'Attendance Sheet removed . '], 200);
	}
}
