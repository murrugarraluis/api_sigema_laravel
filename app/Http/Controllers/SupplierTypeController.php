<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Http\Resources\SupplierTypeResource;
use App\Models\SupplierType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $supplier_types = SupplierType::all()->sortByDesc('created_at');
        return SupplierTypeResource::collection($supplier_types);
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
     * @param SupplierType $supplierType
     * @return SupplierTypeResource
     */
    public function show(SupplierType $supplierType): SupplierTypeResource
    {
        return new SupplierTypeResource($supplierType);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param SupplierType $supplierType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierType $supplierType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SupplierType $supplierType
     * @return JsonResponse
     */
    public function destroy(SupplierType $supplierType): JsonResponse
    {
        $supplierType->delete();
        return response()->json(['message'=>'Supplier Type removed.'],200);
    }
}
