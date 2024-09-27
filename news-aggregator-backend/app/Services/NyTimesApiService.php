<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NyTimesApiService extends BaseApiService
{
    const CACHE_KEY_LAST_FETCHED_DATE = 'nytimes_last_fetched_date';
    const PAGE_SIZE = 50;

    public function fetch()
    {
        $url = 'https://api.nytimes.com/svc/topstories/v2/home.json';
        $articlesData = [];
        $articleCategoryData = [];
        $existingCategories = [];

        $lastFetchedDate = Cache::get(self::CACHE_KEY_LAST_FETCHED_DATE, null);
        Log::info("Fetching New York Times API data. Last fetched date: " . ($lastFetchedDate ?? 'None'));

        $responseData = $this->fetchApiData($url, ['api-key' => config('services.nytimes.key')]);

        foreach ($responseData['results'] as $articleData) {
            $sourceId = $this->processSources('The New York Times');
            $publishedAt = Carbon::parse($articleData['published_date'])->format('Y-m-d H:i:s');
            $articleSlug = Str::slug($articleData['title']);

            $author = $articleData['byline'] ?? null;
            $title = $articleData['title'];
            $url = $articleData['url'];
            $description = $articleData['abstract'] ?? null;
            $content = $articleData['abstract'] ?? null;
            $urlToImage = null;

            $articlesData[] = $this->prepareArticleData($sourceId, $author, $title, $articleSlug, $url, $description, $content, $urlToImage, $publishedAt);

            if (isset($articleData['section'])) {
                $categoryData = $this->processCategories($articleData['section']);
                $articleCategoryData[] = $this->prepareArticleCategoryData($articleSlug, $categoryData['categorySlug']);
            }
        }

        $existingCategories = $categoryData['existingCategories'];

        if (!empty($articlesData)) {
            $articleIds = $this->insertArticles($articlesData);

            $this->updatePivotTable($articleCategoryData, $articleIds, $existingCategories);

            $latestPublishedAt = Carbon::parse($responseData['results'][0]['published_date'])->format('Y-m-d H:i:s');
            Cache::put(self::CACHE_KEY_LAST_FETCHED_DATE, $latestPublishedAt, now()->addDays(1));

        }

        return $responseData['results'];
    }
}
