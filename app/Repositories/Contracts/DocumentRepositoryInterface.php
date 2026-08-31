<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface DocumentRepositoryInterface
{
    public function getUserDocuments(int $userId): Collection;
    public function createDocument(array $data): Model;
    public function deleteDocument(int $documentId, int $userId): bool;
}
