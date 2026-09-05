<?php

namespace App\Services;

use App\Repositories\Contracts\SeoMetaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SeoMetaService
{
    protected SeoMetaRepositoryInterface $seoMetaRepository;

    public function __construct(SeoMetaRepositoryInterface $seoMetaRepository)
    {
        $this->seoMetaRepository = $seoMetaRepository;
    }

    public function getAllSeoMetas(): Collection
    {
        return $this->seoMetaRepository->all();
    }

    public function getSeoMetaById(int $id): ?Model
    {
        return $this->seoMetaRepository->find($id);
    }

    public function createSeoMeta(array $data): Model
    {
        return $this->seoMetaRepository->create($data);
    }

    public function updateSeoMeta(int $id, array $data): bool
    {
        return $this->seoMetaRepository->update($id, $data);
    }

    public function deleteSeoMeta(int $id): bool
    {
        return $this->seoMetaRepository->delete($id);
    }

    public function getSeoMetaByField(string $field, mixed $value): ?Model
    {
        return $this->seoMetaRepository->findByField($field, $value);
    }

    public function countSeoMetas(): int
    {
        return $this->seoMetaRepository->count();
    }
}
