<?php

namespace App\Services;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ArticleService
{
    protected ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getAllArticles(): Collection
    {
        return $this->articleRepository->all();
    }

    public function getArticleById(int $id): ?Model
    {
        return $this->articleRepository->find($id);
    }

    public function createArticle(array $data): Model
    {
        return $this->articleRepository->create($data);
    }

    public function updateArticle(int $id, array $data): bool
    {
        return $this->articleRepository->update($id, $data);
    }

    public function deleteArticle(int $id): bool
    {
        return $this->articleRepository->delete($id);
    }

    public function getArticleByField(string $field, mixed $value): ?Model
    {
        return $this->articleRepository->findByField($field, $value);
    }

    public function countArticles(): int
    {
        return $this->articleRepository->count();
    }
}
