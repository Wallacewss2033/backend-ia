<?php

namespace App\Services;

use App\Jobs\ProcessDocumentJob;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function __construct(
        protected DocumentRepositoryInterface $documentRepository
    ) {}

    public function getUserDocuments(int $userId)
    {
        return $this->documentRepository->getUserDocuments($userId);
    }

    public function uploadDocument(UploadedFile $file, int $userId)
    {
        $path = $file->store('documents', 'public');

        $data = [
            'user_id' => $userId,
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'file_path' => $path,
            'status' => 'pending',
            'total_chunks' => 0,
        ];

        $document = $this->documentRepository->createDocument($data);
        
        ProcessDocumentJob::dispatch($document);

        return $document;
    }

    public function deleteDocument(int $documentId, int $userId): bool
    {
        $document = $this->documentRepository->getUserDocuments($userId)->where('id', $documentId)->first();
        
        if ($document) {
            // Remove file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            return $this->documentRepository->deleteDocument($documentId, $userId);
        }

        return false;
    }
}
