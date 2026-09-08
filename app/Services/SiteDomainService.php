<?php

namespace App\Services;

use App\Repositories\Contracts\SiteDomainRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SiteDomainService
{
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

    public function uploadImageToFirebase(UploadedFile $file, string $type): array
    {
        if ($type === 'favicon') {
            $imageInfo = getimagesize($file->getPathname());
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            if ($width !== $height) {
                throw new \Exception('O favicon deve ter formato quadrado (proporção 1:1).');
            }
            if ($width > 512 || $height > 512) {
                throw new \Exception('O tamanho do favicon deve ser de no máximo 512x512 pixels.');
            }
        }

        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $firebasePath = 'sites-domains/' . $type . '/' . $filename;

        $storage = app('firebase.storage');
        $bucket = $storage->getBucket();

        $bucket->upload(
            fopen($file->getPathname(), 'r'),
            [
                'name' => $firebasePath
            ]
        );

        $url = "https://firebasestorage.googleapis.com/v0/b/" . $bucket->name() . "/o/" . urlencode($firebasePath) . "?alt=media";

        return [
            'url' => $url,
            'path' => $firebasePath
        ];
    }
}
