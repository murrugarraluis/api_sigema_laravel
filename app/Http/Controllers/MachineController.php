<?php

namespace App\Http\Controllers;

use App\Http\Requests\MachineRequest;
use App\Http\Requests\MachineUpdateRequest;
use App\Http\Resources\MachineResource;
use App\Http\Resources\MachinetDetailResource;
use App\Models\Article;
use App\Models\Machine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class MachineController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return AnonymousResourceCollection
	 */
	public function index(): AnonymousResourceCollection
	{
		$machines = Machine::all()->sortByDesc('created_at');
		return MachineResource::collection($machines);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return JsonResponse|Response|object
	 */
	public function store(MachineRequest $request)
	{
		DB::beginTransaction();
		try {
			//          CREATE MACHINE
			$machine = Machine::create($request->except(['articles', 'image', 'technical_sheet']));
			$this->addImage($machine, $request->image);
			$this->addTechnicalSheet($machine, $request->technical_sheet);

			//            ATTACH ARTICLES
			if ($request->articles) {
				$articles = [];
				array_map(function ($article) use (&$articles) {
					$article_id = $article['id'];
					$articles[] = $article_id;
				}, $request->articles);
				$machine->articles()->attach($articles);
			}
			DB::commit();
			return (new MachinetDetailResource($machine))
				->additional(['message' => 'Machine created.'])
				->response()
				->setStatusCode(201);
		} catch (\Exception $e) {
			DB::rollback();
			//            dd($e->getMessage());
			throw new BadRequestException($e->getMessage());
		}
	}

	public function addImage(Machine $machine, $path)
	{
		if (!$path) return;
		$machine->image()->create(['path' => $path]);
	}

	public function addTechnicalSheet(Machine $machine, $path)
	{
		if (!$path) return;
		$machine->technical_sheet()->create(['path' => $path]);
	}

	public function updateImage(Machine $machine, $path)
	{
		if (!$path) return;
		if (!$machine->image) {
			$this->addImage($machine, $path);
			return;
		}
		if ($path == $machine->image->path) return;
		if (Storage::exists("public/" . $machine->image->path)) Storage::delete("public/" . $machine->image->path);
		$machine->image()->update(['path' => $path]);
	}

	public function updateTechnicalSheet(Machine $machine, $path)
	{
		if (!$path) return;
		if (!$machine->technical_sheet) {
			$this->addTechnicalSheet($machine, $path);
			return;
		}
		if ($path == $machine->technical_sheet->path) return;
		if (Storage::exists("public/" . $machine->technical_sheet->path)) Storage::delete("public/" . $machine->technical_sheet->path);
		$machine->technical_sheet()->update(['path' => $path]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Machine $machine
	 * @return MachinetDetailResource
	 */
	public function show(Machine $machine): MachinetDetailResource
	{
		return new MachinetDetailResource($machine);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param MachineUpdateRequest $request
	 * @param Machine $machine
	 * @return MachinetDetailResource
	 */
	public function update(MachineUpdateRequest $request, Machine $machine): MachinetDetailResource
	{
		DB::beginTransaction();
		try {
			//          UPDATE MACHINE
			$machine->update($request->except(['articles', 'image']));
			$this->updateImage($machine, $request->image);
			$this->updateTechnicalSheet($machine, $request->technical_sheet);
			//            ATTACH ARTICLES
			if ($request->articles) {
				$articles = [];
				array_map(function ($article) use (&$articles) {
					$article_id = $article['id'];
					$articles[] = $article_id;
				}, $request->articles);
				$machine->articles()->sync($articles);
			}
			$machine = Machine::find($machine->id);
			DB::commit();
			return (new MachinetDetailResource($machine))->additional(['message' => 'Machine updated.']);
		} catch (\Exception $e) {
			DB::rollback();
			//            dd($e->getMessage());
			throw new BadRequestException($e->getMessage());
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Machine $machine
	 * @return JsonResponse
	 */
	public function destroy(Machine $machine): JsonResponse
	{
		$machine->delete();
		return response()->json(['message' => 'Machine removed.'], 200);
	}
}
