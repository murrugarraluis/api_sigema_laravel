<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentTypeResource;
use App\Models\DocumentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $document_types = DocumentType::all()->sortByDesc('created_at');
        return DocumentTypeResource::collection($document_types);
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
     * @param DocumentType $documentType
     * @return DocumentTypeResource
     */
    public function show(DocumentType $documentType): DocumentTypeResource
    {
        return new DocumentTypeResource($documentType);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param DocumentType $documentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentType $documentType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DocumentType $documentType
     * @return JsonResponse
     */
    public function destroy(DocumentType $documentType): JsonResponse
    {
        $documentType->delete();
        return response()->json(['message'=>'Document Type removed.'],200);
    }
}
