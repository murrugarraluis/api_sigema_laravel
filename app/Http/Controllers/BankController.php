<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $banks = Bank::all()->sortByDesc('created_at');
        return BankResource::collection($banks);
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
     * @param Bank $bank
     * @return BankResource
     */
    public function show(Bank $bank): BankResource
    {
        return new BankResource($bank);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Bank $bank
     * @return JsonResponse
     */
    public function destroy(Bank $bank): JsonResponse
    {
        $bank->delete();
        return response()->json(['message'=>'Bank removed.'],200);
    }
}
