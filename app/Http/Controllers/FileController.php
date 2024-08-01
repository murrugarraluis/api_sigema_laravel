<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function upload(FileRequest $request): \Illuminate\Http\JsonResponse
    {
        $path = $request->file->storeAs('public/files', Str::uuid()->toString() . '.' . $request->file->extension());
        $path = (substr($path,7,strlen($path)));
        return response()->json(['path' => $path]);
    }
}
