<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Requests\UploadAuthorImageRequest;
use App\Http\Resources\AuthorResource;
use App\Services\AuthorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorController extends Controller
{
    protected AuthorService $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }

    public function index(): AnonymousResourceCollection
    {
        $authors = $this->authorService->getAllAuthors();
        return AuthorResource::collection($authors);
    }

    public function store(StoreAuthorRequest $request): AuthorResource
    {
        $author = $this->authorService->createAuthor($request->validated());
        return new AuthorResource($author);
    }

    public function show(int $id): AuthorResource
    {
        $author = $this->authorService->getAuthorById($id);
        return new AuthorResource($author);
    }

    public function update(UpdateAuthorRequest $request, int $id): JsonResponse
    {
        $this->authorService->updateAuthor($id, $request->validated());
        $author = $this->authorService->getAuthorById($id);
        
        return response()->json(new AuthorResource($author));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorService->deleteAuthor($id);
        return response()->json(null, 204);
    }

    public function uploadImage(UploadAuthorImageRequest $request): JsonResponse
    {
        try {
            $result = $this->authorService->uploadImageToFirebase(
                $request->file('image'),
                $request->input('type'),
                'authors'
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
