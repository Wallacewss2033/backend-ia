<?php

namespace App\Repositories\Eloquent;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ArticleRepository implements ArticleRepositoryInterface
{
    protected Article $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['siteDomain', 'author', 'category'])->get();
    }

    public function find(int $id): ?Model
    {
        return $this->model->with(['siteDomain', 'author', 'category'])->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $article = $this->model->findOrFail($id);
        return $article->update($data);
    }

    public function delete(int $id): bool
    {
        $article = $this->model->findOrFail($id);
        return $article->delete();
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
