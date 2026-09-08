<?php

namespace App\Services;

use App\Repositories\Contracts\SiteDomainRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadsImagesToFirebase;

class SiteDomainService
{
    use UploadsImagesToFirebase;
    protected SiteDomainRepositoryInterface $siteDomainRepository;

    public function __construct(SiteDomainRepositoryInterface $siteDomainRepository)
    {
        $this->siteDomainRepository = $siteDomainRepository;
    }

    public function getAllSiteDomains(): Collection
    {
        return $this->siteDomainRepository->all();
    }

    public function getSiteDomainById(int $id): ?Model
    {
        return $this->siteDomainRepository->find($id);
    }

    public function createSiteDomain(array $data): Model
    {
        return $this->siteDomainRepository->create($data);
    }

    public function updateSiteDomain(int $id, array $data): bool
    {
        return $this->siteDomainRepository->update($id, $data);
    }

    public function deleteSiteDomain(int $id): bool
    {
        return $this->siteDomainRepository->delete($id);
    }

    public function getSiteDomainByField(string $field, mixed $value): ?Model
    {
        return $this->siteDomainRepository->findByField($field, $value);
    }

    public function countSiteDomains(): int
    {
        return $this->siteDomainRepository->count();
    }


}
