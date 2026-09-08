<?php

namespace App\Services;

use App\Repositories\Contracts\AuthorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadsImagesToFirebase;

class AuthorService
{
    use UploadsImagesToFirebase;
    protected AuthorRepositoryInterface $authorRepository;

    public function __construct(AuthorRepositoryInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function getAllAuthors(): Collection
    {
        return $this->authorRepository->all();
    }

    public function getAuthorById(int $id): ?Model
    {
        return $this->authorRepository->find($id);
    }

    public function createAuthor(array $data): Model
    {
        return $this->authorRepository->create($data);
    }

    public function updateAuthor(int $id, array $data): bool
    {
        return $this->authorRepository->update($id, $data);
    }

    public function deleteAuthor(int $id): bool
    {
        return $this->authorRepository->delete($id);
    }

    public function getAuthorByField(string $field, mixed $value): ?Model
    {
        return $this->authorRepository->findByField($field, $value);
    }

    public function countAuthors(): int
    {
        return $this->authorRepository->count();
    }
}
