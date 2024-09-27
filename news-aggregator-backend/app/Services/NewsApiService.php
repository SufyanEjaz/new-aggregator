<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsApiService extends BaseApiService
{
    protected $categories = [
        'business',
        'entertainment',
        'general',
        'health',
        'science',
        'sports',
        'technology'
    ];

    const CACHE_KEY_LAST_PAGE = 'newsapi_last_page';
    const PAGE_SIZE = 50;

    public function fetch()
    {
        $url = 'https://newsapi.org/v2/top-headlines';
        $articlesData = [];
        $articleCategoryData = [];
        $existingCategories = [];
        
        foreach ($this->categories as $category) {

            $lastPage = Cache::get(self::CACHE_KEY_LAST_PAGE . "_{$category}", 1);
            Log::info("Fetching News API data for category {$category}, page {$lastPage}");

            $responseData = $this->fetchApiData($url, [
                'apiKey' => config('services.newsapi.key'),
                'language' => 'en',
                'pageSize' => self::PAGE_SIZE,
                'category' => $category,
                'page' => $lastPage
            ]);

            foreach ($responseData['articles'] as $articleData) {
                $sourceId = $this->processSources($articleData['source']['name']);
                $publishedAt = Carbon::parse($articleData['publishedAt'])->format('Y-m-d H:i:s');
                $articleSlug = Str::slug($articleData['title']);

                $articlesData[] = [
                    'source_id' => $sourceId,
                    'author' => $articleData['author'] ?? null,
                    'title' => $articleData['title'],
                    'slug' => $articleSlug,
                    'url' => $articleData['url'],
                    'description' => $articleData['description'] ?? null,
                    'content' => $articleData['content'] ?? null,
                    'url_to_image' => $articleData['urlToImage'] ?? null,
                    'published_at' => $publishedAt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $categoryData = $this->processCategories($category);
                $articleCategoryData[] = $this->prepareArticleCategoryData($articleSlug, $categoryData['categorySlug']);
            }

            if (!empty($responseData['articles'])) {
                Cache::put(self::CACHE_KEY_LAST_PAGE . "_{$category}", $lastPage + 1, now()->addDays(1));
            }

            $existingCategories = $categoryData['existingCategories'] ?? [];
        }

        if (!empty($articlesData)) {

            $articleIds = $this->insertArticles($articlesData);
            $this->updatePivotTable($articleCategoryData, $articleIds, $existingCategories);

        } else {
            Log::info("No new articles found for News API categories.");
        }

        return $articlesData;
    }
}
