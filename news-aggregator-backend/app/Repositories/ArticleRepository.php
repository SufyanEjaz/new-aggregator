<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function getFilteredArticles(array $filters = [])
    {
        try {
            $query = Article::query();

            // Filter by keyword in title or description
            if (!empty($filters['keyword'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
                });
            }

            // Filter by date
            if (!empty($filters['date'])) {
                $query->whereDate('published_at', $filters['date']);
            }
        
            // Filter by categories (join with article_category)
            if (!empty($filters['category_ids'])) {
                $query->whereHas('categories', function ($q) use ($filters) {
                    $q->whereIn('categories.id', $filters['category_ids']);
                });
            }
        
            // Filter by sources
            if (!empty($filters['source_ids'])) {
                $query->whereIn('source_id', $filters['source_ids']);
            }
        
            // Filter by authors
            if (!empty($filters['authors'])) {
                $query->whereIn('author', $filters['authors']);
            }

            // Order the results by published date, most recent first
            return $query->with(['source:id,name', 'categories:id,name'])
                        ->orderBy('published_at', 'desc')
                        ->paginate();

        } catch (\Throwable $e) {
            throw new \Exception('Error fetching articles');
        }
    }

    public function getArticleById(int $articleId)
    {
        try {
            // Fetch the article with its associated categories
            return Article::with(['source:id,name', 'categories:id,name'])->findOrFail($articleId);

        } catch (\Throwable $e) {
            throw new \Exception('Error fetching articles');
        }
    }
}