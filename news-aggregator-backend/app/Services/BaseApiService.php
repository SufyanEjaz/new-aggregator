<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

abstract class BaseApiService
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    // Fetch data from an API
    protected function fetchApiData($url, $query)
    {
        try {
            $response = $this->httpClient->get($url, ['query' => $query]);
            return json_decode($response->getBody(), true);

        } catch (Exception $e) {
            Log::error("Error fetching data from API: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new Exception('Failed to fetch data from API', 0, $e);
        }

    }

    // Insert or update sources in the database
    protected function processSources($sourceName)
    {
        $sourceSlug = Str::slug($sourceName);
        $existingSources = DB::table('sources')->pluck('id', 'slug')->toArray();

        if (!isset($existingSources[$sourceSlug])) {
            DB::table('sources')->insert([
                'name' => $sourceName,
                'slug' => $sourceSlug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // Refresh source list after insertion
            $existingSources = DB::table('sources')->pluck('id', 'slug')->toArray();
        }

        return $existingSources[$sourceSlug];
    }

     // General method to process article data
     protected function prepareArticleData($sourceId, $author, $title, $slug, $url, $description, $content, $urlToImage, $publishedAt)
     {
         return [
            'source_id' => $sourceId,
            'author' => $author,
            'title' => $title,
            'slug' => $slug,
            'url' => $url,
            'description' => $description,
            'content' => $content,
            'url_to_image' => $urlToImage,
            'published_at' => $publishedAt,
            'created_at' => now(),
            'updated_at' => now(),
         ];
     }

    // Insert or update categories in the database
    protected function processCategories($categoryName)
    {
        $categorySlug = Str::slug($categoryName);
        $existingCategories = DB::table('categories')->pluck('id', 'slug')->toArray();

        if (!isset($existingCategories[$categorySlug])) {
            DB::table('categories')->insert([
                'name' => $categoryName,
                'slug' => $categorySlug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // Refresh category list after insertion
            $existingCategories = DB::table('categories')->pluck('id', 'slug')->toArray();
        }

        return ['categorySlug' => $categorySlug, 'existingCategories' => $existingCategories];
        // return $existingCategories[$categorySlug];
    }

    // General method to process article Categories
    protected function prepareArticleCategoryData($articleSlug, $categorySlug)
    {
        return [
            'article_slug' => $articleSlug,
            'category_slug' => $categorySlug,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Insert articles in bulk
    protected function insertArticles($articlesData)
    {
        DB::table('articles')->insertOrIgnore($articlesData);
        return DB::table('articles')->pluck('id', 'slug')->toArray();
    }

     
    // Update the pivot table `article_category` with `article_id` and `category_id`
    protected function updatePivotTable($articleCategoryData, $articleIds, $existingCategories)
    {
        foreach ($articleCategoryData as &$pivot) {
            $pivot['article_id'] = $articleIds[$pivot['article_slug']] ?? null;
            $pivot['category_id'] = $existingCategories[$pivot['category_slug']] ?? null;

            // Remove the slug placeholders after mapping to actual IDs
            unset($pivot['article_slug']);
            unset($pivot['category_slug']);
        }

        // Insert article-category relationships into the pivot table
        $this->insertArticleCategories($articleCategoryData);
    }


    // Insert article categories
    protected function insertArticleCategories($articleCategoryData)
    {
        DB::table('article_category')->insertOrIgnore($articleCategoryData);
    }
}
