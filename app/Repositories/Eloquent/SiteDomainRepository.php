<?php

namespace App\Repositories\Eloquent;

use App\Models\SiteDomain;
use App\Repositories\Contracts\SiteDomainRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SiteDomainRepository implements SiteDomainRepositoryInterface
{
    protected SiteDomain $model;

    public function __construct(SiteDomain $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $siteDomain = $this->model->findOrFail($id);
        return $siteDomain->update($data);
    }

    public function delete(int $id): bool
    {
        $siteDomain = $this->model->findOrFail($id);
        return $siteDomain->delete();
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
