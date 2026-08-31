<?php

namespace App\Repositories\Eloquent;

use App\Models\Document;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DocumentRepository implements DocumentRepositoryInterface
{
    public function getUserDocuments(int $userId): Collection
    {
        return Document::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function createDocument(array $data): Model
    {
        return Document::create($data);
    }

    public function deleteDocument(int $documentId, int $userId): bool
    {
        $document = Document::where('id', $documentId)
            ->where('user_id', $userId)
            ->first();

        if ($document) {
            return $document->delete();
        }

        return false;
    }
}
