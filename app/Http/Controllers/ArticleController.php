<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Http\Resources\ArticleDetailResource;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\ArticleType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $articles = Article::all()->sortByDesc('created_at');
        if ($request->article_type) {
            $article_type = ArticleType::where('name', $request->article_type)->first();
            $articles = Article::whereBelongsTo($article_type, 'article_type')->get()->sortByDesc('created_at');
        }
        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ArticleRequest $request
     * @return JsonResponse|object
     */
    public function store(ArticleRequest $request)
    {
        DB::beginTransaction();
        try {
            //            CREATE ARTICLE
            $article = Article::create([
                'serie_number' => $request->serie_number,
                'name' => $request->name,
                'brand' => $request->brand,
                'model' => $request->model,
                'quantity' => $request->quantity,
                'article_type_id' => $request->article_type["id"],
            ]);
            $this->addImage($article, $request->image);
            $this->addTechnicalSheet($article, $request->technical_sheet);
            //            ATTACH SUPPLIERS
            if ($request->suppliers) {
                $suppliers = [];
                array_map(function ($supplier) use (&$suppliers) {
                    $supplier_id = $supplier['id'];
                    $price = $supplier['price'];
                    $suppliers[$supplier_id] = ["price" => $price];
                }, $request->suppliers);

                $article->suppliers()->attach($suppliers);
            }

            DB::commit();
            return (new ArticleDetailResource($article))
                ->additional(['message' => 'Article created.'])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestException($e->getMessage());
        }
    }

    public function addImage(Article $article, $path)
    {
        if (!$path) return;
        $article->image()->create(['path' => $path]);
    }

    public function addTechnicalSheet(Article $article, $path)
    {
        if (!$path) return;
        $article->technical_sheet()->create(['path' => $path]);
    }

    public function updateImage(Article $article, $path)
    {
        if (!$path) return;
        if (!$article->image) {
            $this->addImage($article, $path);
            return;
        }
        if ($path == $article->image->path) return;
        if (Storage::exists("public/" . $article->image->path)) Storage::delete("public/" . $article->image->path);
        $article->image()->update(['path' => $path]);
    }

    public function updateTechnicalSheet(Article $article, $path)
    {
        if (!$path) return;
        if (!$article->technical_sheet) {
            $this->addTechnicalSheet($article, $path);
            return;
        }
        if ($path == $article->technical_sheet->path) return;
        if (Storage::exists("public/" . $article->technical_sheet->path)) Storage::delete("public/" . $article->technical_sheet->path);
        $article->technical_sheet()->update(['path' => $path]);
    }

    /**
     * Display the specified resource.
     *
     * @param Article $article
     * @return ArticleDetailResource
     */
    public function show(Article $article): ArticleDetailResource
    {
        return new ArticleDetailResource($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ArticleUpdateRequest $request
     * @param Article $article
     * @return ArticleDetailResource
     */
    public function update(ArticleUpdateRequest $request, Article $article): ArticleDetailResource
    {
        DB::beginTransaction();
        try {
            //            UPDATE ARTICLE
            $article->update([
                'serie_number' => $request->serie_number,
                'name' => $request->name,
                'brand' => $request->brand,
                'model' => $request->model,
                'quantity' => $request->quantity,
                'article_type_id' => $request->article_type["id"],
            ]);
            $this->updateImage($article, $request->image);
            $this->updateTechnicalSheet($article, $request->technical_sheet);

            //            ATTACH SUPPLIERS
            if ($request->suppliers) {
                $suppliers = [];
                array_map(function ($supplier) use (&$suppliers) {
                    $supplier_id = $supplier['id'];
                    $price = $supplier['price'];
                    $suppliers[$supplier_id] = ["price" => $price];
                }, $request->suppliers);

                $article->suppliers()->sync($suppliers);
            }
            $article = Article::find($article->id);

            DB::commit();
            return (new ArticleDetailResource($article))->additional(['message' => 'Article updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestException($e->getMessage());
        }
    }

    /**A
     * Remove the specified resource from storage.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function destroy(Article $article): JsonResponse
    {
        $article->delete();
        return response()->json(['message' => 'Article removed.'], 200);
    }
}
