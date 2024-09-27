<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GuardianApiService extends BaseApiService
{   
    const CACHE_KEY_LAST_PAGE = 'guardian_api_last_page';
    const PAGE_SIZE = 100;
    public function fetch()
    {
        $url = 'https://content.guardianapis.com/search';
        $articlesData = [];
        $articleCategoryData = [];
        $existingCategories = [];

        $lastPage = Cache::get(self::CACHE_KEY_LAST_PAGE, 1);
        Log::info("Fetching Guardian API data for page {$lastPage}");

        $responseData = $this->fetchApiData($url, [
            'api-key' => config('services.guardian.key'),
            'show-fields' => 'all',
            'page-size' => self::PAGE_SIZE,
            'page' => $lastPage,
        ]);

        foreach ($responseData['response']['results'] as $articleData) {
            $sourceId = $this->processSources('The Guardian');
            $publishedAt = Carbon::parse($articleData['webPublicationDate'])->format('Y-m-d H:i:s');
            $articleSlug = Str::slug($articleData['webTitle']);

            $author = $articleData['fields']['byline'] ?? null;
            $title = $articleData['webTitle'];
            $url = $articleData['webUrl'];
            $description = $articleData['fields']['trailText'] ?? null;
            $content = $articleData['fields']['bodyText'] ?? null;
            $urlToImage = $articleData['fields']['thumbnail'] ?? null;

            $articlesData[] = $this->prepareArticleData($sourceId, $author, $title, $articleSlug, $url, $description, $content, $urlToImage, $publishedAt);

            if (isset($articleData['sectionName'])) {
                $categoryData = $this->processCategories($articleData['sectionName']);
                $articleCategoryData[] = $this->prepareArticleCategoryData($articleSlug, $categoryData['categorySlug']);
            }
        }

        $existingCategories = $categoryData['existingCategories'];

        $articleIds = $this->insertArticles($articlesData);

        $this->updatePivotTable($articleCategoryData, $articleIds, $existingCategories);

        if (count($responseData['response']['results']) > 0) {
            Cache::put(self::CACHE_KEY_LAST_PAGE, $lastPage + 1, now()->addDays(1));  // Add TTL for 1 day
        }

        return $responseData['response']['results'];
    }
}
