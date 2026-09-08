<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UploadArticleImageRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(): AnonymousResourceCollection
    {
        $articles = $this->articleService->getAllArticles();
        return ArticleResource::collection($articles);
    }

    public function store(StoreArticleRequest $request): ArticleResource
    {
        $article = $this->articleService->createArticle($request->validated());
        return new ArticleResource($article);
    }

    public function show(int $id): ArticleResource
    {
        $article = $this->articleService->getArticleById($id);
        return new ArticleResource($article);
    }

    public function update(UpdateArticleRequest $request, int $id): JsonResponse
    {
        $this->articleService->updateArticle($id, $request->validated());
        $article = $this->articleService->getArticleById($id);
        
        return response()->json(new ArticleResource($article));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->articleService->deleteArticle($id);
        return response()->json(null, 204);
    }

    public function uploadImage(UploadArticleImageRequest $request): JsonResponse
    {
        try {
            $result = $this->articleService->uploadImageToFirebase(
                $request->file('image'),
                $request->input('type'),
                'articles'
            );

            return response()->json([
                'message' => 'Upload realizado com sucesso!',
                'url' => $result['url'],
                'path' => $result['path']
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
