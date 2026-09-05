<?php

namespace App\Repositories\Eloquent;

use App\Models\SeoMeta;
use App\Repositories\Contracts\SeoMetaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SeoMetaRepository implements SeoMetaRepositoryInterface
{
    protected SeoMeta $model;

    public function __construct(SeoMeta $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('article')->get();
    }

    public function find(int $id): ?Model
    {
        return $this->model->with('article')->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $seoMeta = $this->model->findOrFail($id);
        return $seoMeta->update($data);
    }

    public function delete(int $id): bool
    {
        $seoMeta = $this->model->findOrFail($id);
        return $seoMeta->delete();
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
