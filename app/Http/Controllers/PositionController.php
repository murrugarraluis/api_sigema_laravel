<?php

namespace App\Http\Controllers;

use App\Http\Resources\PositionResource;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $positions = Position::all()->sortByDesc('created_at');
        return PositionResource::collection($positions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Position $position
     * @return PositionResource
     */
    public function show(Position $position): PositionResource
    {
        return new PositionResource($position);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Position $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Position $position
     * @return JsonResponse
     */
    public function destroy(Position $position): JsonResponse
    {
        $position->delete();
        return response()->json(['message'=>'Position removed.'],200);
    }
}
