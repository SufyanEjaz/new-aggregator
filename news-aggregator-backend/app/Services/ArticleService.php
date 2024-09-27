<?php

namespace App\Services;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ArticleService
{
    protected $articleRepo;

    public function __construct(ArticleRepositoryInterface $articleRepo)
    {
        $this->articleRepo = $articleRepo;
    }

    public function getArticles(array $filters = [])
    {
        $cacheKey = 'articles_' . md5(serialize($filters));
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($filters) {
            return $this->articleRepo->getFilteredArticles($filters);
        });
    }

    public function getArticleById(int $articleId)
    {
        $cacheKey = 'articles_' . md5(serialize($articleId));
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($articleId) {
            return $this->articleRepo->getArticleById($articleId);
        });
    }
}