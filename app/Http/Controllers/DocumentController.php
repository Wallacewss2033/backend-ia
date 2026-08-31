<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $documentService
    ) {}

    public function index(): JsonResponse
    {
        $documents = $this->documentService->getUserDocuments(auth()->id());
        
        return response()->json(DocumentResource::collection($documents));
    }

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $document = $this->documentService->uploadDocument($request->file('file'), auth()->id());
        
        return response()->json(new DocumentResource($document), 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->documentService->deleteDocument($id, auth()->id());

        if ($deleted) {
            return response()->json(null, 204);
        }

        return response()->json(['message' => 'Document not found or unauthorized'], 404);
    }
}
