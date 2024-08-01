<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenancePDFRequest;
use App\Http\Requests\MaintenanceSheetStoreRequest;
use App\Http\Resources\MachinesDetailPDFResource;
use App\Http\Resources\MachinesResumenPDFResource;
use App\Http\Resources\MaintenanceSheetDetailResource;
use App\Http\Resources\MaintenanceSheetResource;
use App\Models\Article;
use App\Models\Machine;
use App\Models\MaintenanceSheet;
use Carbon\Carbon;
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
use Barryvdh\DomPDF\Facade\Pdf;

class MaintenanceSheetController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return AnonymousResourceCollection
	 */
	public function index(Request $request): AnonymousResourceCollection
	{
		$maintenance_sheets = MaintenanceSheet::with([
			'maintenance_sheet_details',
			'maintenance_type',
			'supplier',
//			'supplier.supplier_type',
//			'supplier.document_type',
			'machine.technical_sheet',
		])
			->take(300)
			->get()->sortByDesc('created_at');
		if ($request->start_date && $request->end_date) {
			$maintenance_sheets = MaintenanceSheet::whereDate('date', '>=', $request->start_date)->whereDate('date', '<=', $request->end_date)
				->get()->sortByDesc('created_at');
		}
		return MaintenanceSheetResource::collection($maintenance_sheets);
	}

	public function index_pdf(MaintenancePDFRequest $request)
	{
//		$total_maintenances = 0;
		$machines = Machine::with([
			'maintenance_sheets',
			'maintenance_sheets.maintenance_sheet_details',
			'maintenance_sheets.maintenance_type',
			'maintenance_sheets.supplier',
		])
			->whereHas('maintenance_sheets', function (Builder $query) use ($request) {
				$query->whereDate('maintenance_sheets.date', '>=', $request->start_date)
					->whereDate('maintenance_sheets.date', '<=', $request->end_date);
			})->get();


		if ($request->type == "resumen") $report = MachinesResumenPDFResource::collection($machines);
		else $report = MachinesDetailPDFResource::collection($machines);

//		$total_maintenances = $report->count();

//		dd($report);
		if ($request->order_by == "asc") {
			$report = $report->sortBy(function ($item) use ($request) {
				return ($item->jsonSerialize()[$request->sort_by]);
			})->values();
		} else {
			$report = $report->sortByDesc(function ($item) use ($request) {
				return ($item->jsonSerialize()[$request->sort_by]);
			})->values();
		}

		$data = [
			"data" => $report->jsonSerialize(),
			"total_machines" => MachinesResumenPDFResource::collection($machines)->count(),
			"total_maintenances" => MachinesResumenPDFResource::collection($machines)->sum(function ($machine) {
				return ($machine->jsonSerialize()['maintenance_count']);
			}),
			"total_amount" => $machines->sum('amount'),
			"start_date" => $request->start_date,
			"end_date" => $request->end_date,
			"type" => $request->type,
			"sort_by" => $request->sort_by
		];
//		return $data;
		$language = (Auth()->user()->employee->native_language);
		$locale = $language == 'spanish' ? 'es' : 'en';
		App::setLocale($locale);

		$pdf = PDF::loadView('maintenance-report', compact('data'));
		$orientation = $request->type == 'resumen' ? 'portraint' : 'landscape';
		$pdf->setPaper('A4', $orientation);
//        $font = $pdf->getFontMetrics()->get_font("helvetica", "bold");
//        $pdf->getCanvas()->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0,0,0));

//		dd($data);

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
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return JsonResponse|Response|object
	 */
	public function store(MaintenanceSheetStoreRequest $request)
	{
//        dd($request->date);
		DB::beginTransaction();
		try {
//          CREATE
			$maintenance_sheet = MaintenanceSheet::create([
				"date" => $request->date,
				"responsible" => $request->responsible,
				"technical" => $request->technical,
				"description" => $request->description,
				'supplier_id' => $request->supplier["id"],
				'maintenance_type_id' => $request->maintenance_type["id"],
				'machine_id' => $request->machine["id"],
				'ref_invoice_number' => $request->ref_invoice_number,
				"maximum_working_time" => $request->maximum_working_time
			]);
			$details = [];
			$item = 1;
			array_map(function ($detail) use (&$details, &$item) {
				if (array_key_exists('article', $detail)) {
					$article = Article::find($detail['article']['id']);
//                    dd($article->quantity);
					$article->update([
						"quantity" => ($article->quantity - $detail['quantity'])
					]);
				}
				$new = [
					"article_id" => array_key_exists('article', $detail) ? $detail['article']['id'] : null,
					"description" => array_key_exists('description', $detail) ? $detail['description'] : null,
					"price" => $detail['price'],
					"quantity" => $detail['quantity'],
					"item" => $item,
				];
				$details[] = $new;
				$item++;
			}, $request->detail);
			$maintenance_sheet->maintenance_sheet_details()->createMany($details);

			$machine = Machine::find($request->machine["id"]);
			$machine->update([
				"recommendation" => $request->recommendation,
				"maximum_working_time" => $request->maximum_working_time
			]);
			DB::commit();
//            dd($maintenance_sheet);
			return (new MaintenanceSheetDetailResource($maintenance_sheet))
				->additional(['message' => 'Maintenance Sheet created.'])
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
	 * @param MaintenanceSheet $maintenanceSheet
	 * @return MaintenanceSheetDetailResource
	 */
	public function show(MaintenanceSheet $maintenanceSheet): MaintenanceSheetDetailResource
	{
		return new MaintenanceSheetDetailResource($maintenanceSheet);


	}

	public function show_pdf(MaintenanceSheet $maintenanceSheet)
	{
		$language = (Auth()->user()->employee->native_language);
		$locale = $language == 'spanish' ? 'es' : 'en';
		App::setLocale($locale);

		$data = $this->show($maintenanceSheet)->jsonSerialize();
//		dd($data);
		$pdf = PDF::loadView('maintenance-one-report', compact('data'));
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
	 * @param MaintenanceSheet $maintenanceSheet
	 * @return Response
	 */
	public function update(Request $request, MaintenanceSheet $maintenanceSheet)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param MaintenanceSheet $maintenanceSheet
	 * @return JsonResponse
	 */
	public function destroy(MaintenanceSheet $maintenanceSheet): JsonResponse
	{
		$maintenanceSheet->delete();
		return response()->json(['message' => 'Maintenance Sheet removed.'], 200);
	}
}
