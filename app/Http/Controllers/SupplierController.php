<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Resources\SupplierDetailResource;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $suppliers = Supplier::all()->sortByDesc('created_at');
        return SupplierResource::collection($suppliers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse|object
     */
    public function store(SupplierRequest $request)
    {
        DB::beginTransaction();
        try {
//            CREATE SUPPLIER
            $supplier = Supplier::create([
                'document_number' => $request->document_number,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'supplier_type_id' => $request->supplier_type["id"],
                'document_type_id' => $request->document_type["id"],
            ]);
//            ATTACH BANKS
            if ($request->banks) {
                $banks = [];
                array_map(function ($bank) use (&$banks) {
                    $bank_id = $bank['id'];
                    $account_number = $bank['account_number'];
                    $interbank_account_number = $bank['interbank_account_number'];
                    $banks[$bank_id] = ["account_number" => $account_number, "interbank_account_number" => $interbank_account_number];
                }, $request->banks);

                $supplier->banks()->attach($banks);
            }
            DB::commit();
            return (new SupplierDetailResource($supplier))
                ->additional(['message' => 'Supplier created.'])
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
     * @param Supplier $supplier
     * @return SupplierDetailResource
     */
    public function show(Supplier $supplier): SupplierDetailResource
    {
        return new SupplierDetailResource($supplier);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param SupplierRequest $request
     * @param Supplier $supplier
     * @return SupplierDetailResource
     */
    public function update(SupplierUpdateRequest $request, Supplier $supplier): SupplierDetailResource
    {
        DB::beginTransaction();
        try {
//            UPDATE SUPPLIER
            $supplier->update([
                'document_number' => $request->document_number,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'supplier_type_id' => $request->supplier_type["id"],
                'document_type_id' => $request->document_type["id"],
            ]);
//            ATTACH BANKS
            if ($request->banks) {
                $banks = [];
                array_map(function ($bank) use (&$banks) {
                    $bank_id = $bank['id'];
                    $account_number = $bank['account_number'];
                    $interbank_account_number = $bank['interbank_account_number'];
                    $banks[$bank_id] = ["account_number" => $account_number, "interbank_account_number" => $interbank_account_number];
                }, $request->banks);
                $supplier->banks()->sync($banks);
            }
            DB::commit();
            return (new SupplierDetailResource($supplier))->additional(['message' => 'Supplier updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            throw new BadRequestException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Supplier $supplier
     * @return JsonResponse
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();
        return response()->json(['message' => 'Supplier removed.'], 200);
    }
}
