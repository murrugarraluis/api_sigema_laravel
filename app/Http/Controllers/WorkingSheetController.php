<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkStartRequest;
use App\Http\Requests\WorkUpdateRequest;
use App\Http\Resources\WorkingSheetDetailResource;
use App\Http\Resources\WorkingSheetResource;
use App\Models\Machine;
use App\Models\Notification;
use App\Models\User;
use App\Models\WorkingSheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\Builder;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkingSheetController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return AnonymousResourceCollection
	 */
	public function index(Request $request): AnonymousResourceCollection
	{
		$working_sheets = WorkingSheet::all()->sortByDesc('created_at');
		if ($request->start_date && $request->end_date) {
			$working_sheets = WorkingSheet::whereDate('date', '>=', $request->start_date)->whereDate('date', '<=', $request->end_date)
				->get()->sortByDesc('created_at');
		}
		return WorkingSheetResource::collection($working_sheets);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	public function start(WorkStartRequest $request)
	{
		DB::beginTransaction();
		try {
			$working_sheet = WorkingSheet::create([
				'machine_id' => $request->machine["id"],
				'date' => date('Y-m-d H:i:s'),
				'description' => $request->description,
				'is_open' => true
			]);
			$request_restart = new WorkUpdateRequest();
			$request_restart->merge(['date' => $request->date]);
			$this->restart($request_restart, $working_sheet);
//			$this->pushNotifications($request->machine["id"]);
			DB::commit();
			return (new WorkingSheetDetailResource($working_sheet))
				->additional(['message' => 'Work started.'])
				->response()
				->setStatusCode(201);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	public function pushNotifications($machine_id)
	{
//		DATA MACHINE
		$machine = Machine::find($machine_id);
		$machine = app(MachineController::class)->show($machine)->jsonSerialize();
		$maximum_working_time = $machine["maximum_working_time"];
		$maximum_working_time_per_day = $machine["maximum_working_time_per_day"];
		$total_time_used = $machine["total_time_used"];
		$total_time_used_today = $machine["total_time_used_today"];

//		CONVERTER TIMES IN SECONDS
		$total_time_used = $this->converterTimeSeconds($total_time_used["hours"], $total_time_used["minutes"], $total_time_used["seconds"]);
		$total_time_used_today = $this->converterTimeSeconds($total_time_used_today["hours"], $total_time_used_today["minutes"], $total_time_used_today["seconds"]);
		$maximum_working_time = $this->converterTimeSeconds($maximum_working_time, 0, 0);
		$maximum_working_time_per_day = $this->converterTimeSeconds($maximum_working_time_per_day, 0, 0);

//		CALCULATE DATE NOTIFICATION
		$time_limit_global_48 = ($maximum_working_time - $total_time_used) - $this->converterTimeSeconds(48, 0, 0);
		$time_limit_global_6 = ($maximum_working_time - $total_time_used) - $this->converterTimeSeconds(6, 0, 0);
		$time_limit_per_day_1 = ($maximum_working_time_per_day - $total_time_used_today) - $this->converterTimeSeconds(1, 0, 0);


		$date_limit_global_48 = date('Y-m-d H:i:s', (time() + $time_limit_global_48));;
		$date_limit_global_6 = date('Y-m-d H:i:s', (time() + $time_limit_global_6));;
		$date_limit_per_day_1 = date('Y-m-d H:i:s', (time() + $time_limit_per_day_1));;;

		$now = date('Y-m-d H:i:s');
		$users = User::select('id')->with(['roles', 'roles.permissions'])->whereHas('roles.permissions', function ($query) {
			$query->where('name', 'notifications');
		})->get();
		$user_ids = [];
		$users->map(function ($user) use (&$user_ids) {
			$user_ids[] = $user->id;
		});
//		$users = User::with(['roles','roles.permissions'])->get();

//		dd($user_id);
//		notify 48 hours before the limit is reached
		if ($date_limit_global_48 >= $now) {
			$notification = Notification::create([
				"machine_id" => $machine_id,
				"message" => "has 48 hours of working time left",
				"date_send_notification" => $date_limit_global_48
			]);
			$notification->users()->attach($user_ids);
		}
//		notify 6 hours before the limit is reached
		if ($date_limit_global_6 >= $now) {
			$notification = Notification::create([
				"machine_id" => $machine_id,
				"message" => "has 6 hours of working time left",
				"date_send_notification" => $date_limit_global_6
			]);
			$notification->users()->attach($user_ids);
		}

//		notify 1 hours before the limit is reached
		if ($date_limit_per_day_1 >= $now) {
			$notification = Notification::create([
				"machine_id" => $machine_id,
				"message" => "has 1 hours of working time left today",
				"date_send_notification" => $date_limit_per_day_1
			]);
			$notification->users()->attach($user_ids);
		}
	}

	public function deleteNotifications($machine_id)
	{
		Notification::where('is_send', false)->where('machine_id', $machine_id)->delete();
	}

	function converterTimeSeconds($hour, $minute, $second)
	{
		$hours = (int)$hour * 3600;
		$minutes = (int)$minute * 60;
		$seconds = (int)$second;
//		dd();
		return $hours + $minutes + $seconds;
	}

	public function pause(WorkUpdateRequest $request, WorkingSheet $workingSheet)
	{
		DB::beginTransaction();
		try {
//            dd($request->date);
			$last_working_hour = $workingSheet->working_hours()->orderBy('created_at', 'desc')->first();
			if (!$last_working_hour->date_time_end) {
				$last_working_hour->update([
					'date_time_end' => date('Y-m-d H:i:s'),
				]);
			}
			$this->deleteNotifications($workingSheet->machine->id);
			DB::commit();
			return (new WorkingSheetDetailResource($workingSheet))
				->additional(['message' => 'Work paused.']);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	public function restart(WorkUpdateRequest $request, WorkingSheet $workingSheet)
	{
//        dd($request->date);
		DB::beginTransaction();
		try {
			$workingSheet->working_hours()->create([
				'date_time_start' => date('Y-m-d H:i:s'),
			]);
			$this->pushNotifications($workingSheet->machine->id);
			DB::commit();
			return (new WorkingSheetDetailResource($workingSheet))
				->additional(['message' => 'Work restarted.']);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	public function stop(WorkUpdateRequest $request, WorkingSheet $workingSheet)
	{
		DB::beginTransaction();
		try {
			$this->pause($request, $workingSheet);
			$workingSheet->update([
				'is_open' => false
			]);
			DB::commit();
			return (new WorkingSheetDetailResource($workingSheet))
				->additional(['message' => 'Work stopped.']);
		} catch (\Exception $e) {
			DB::rollback();
			throw new BadRequestException($e->getMessage());
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param WorkingSheet $workingSheet
	 * @return WorkingSheetDetailResource
	 */
	public function show(WorkingSheet $workingSheet): WorkingSheetDetailResource
	{
		return new WorkingSheetDetailResource($workingSheet);

	}

	public function show_pdf(WorkingSheet $workingSheet)
	{
		$language = (Auth()->user()->employee->native_language);
		$locale = $language == 'spanish' ? 'es' : 'en';
		App::setLocale($locale);

		$data = $this->show($workingSheet)->jsonSerialize();
		//jsonSerialize for Template Blade
		$data['working_hours'] = array_map(function ($workingHour) {
			return $workingHour->jsonSerialize();
		}, $data['working_hours']);
		$pdf = PDF::loadView('work-one-report', compact('data'));
		$pdf->setPaper('A4');
//		return $pdf->download();

		$name_file = Str::uuid()->toString();
		$path = 'public/reports/' . $name_file . '.pdf';
		Storage::put($path, $pdf->output());
		$path = (substr($path, 7, strlen($path)));

		return [
			'path' => $path
		];

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param WorkingSheet $workingSheet
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, WorkingSheet $workingSheet)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param WorkingSheet $workingSheet
	 * @return JsonResponse
	 */
	public function destroy(WorkingSheet $workingSheet): JsonResponse
	{
		$workingSheet->delete();
		return response()->json(['message' => 'Working Sheet removed.'], 200);
	}
}
