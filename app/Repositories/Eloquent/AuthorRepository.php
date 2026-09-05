<?php

namespace App\Repositories\Eloquent;

use App\Models\Author;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AuthorRepository implements AuthorRepositoryInterface
{
    protected Author $model;

    public function __construct(Author $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('siteDomain')->get();
    }

    public function find(int $id): ?Model
    {
        return $this->model->with('siteDomain')->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $author = $this->model->findOrFail($id);
        return $author->update($data);
    }

    public function delete(int $id): bool
    {
        $author = $this->model->findOrFail($id);
        return $author->delete();
    }

    public function findByField(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    public function count(): int
    {
        return $this->model->count();
    }
}
