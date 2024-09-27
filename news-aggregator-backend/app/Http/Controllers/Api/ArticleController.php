<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        try {

            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $user = Auth::user()->load('preferences');

            $filters = [
                'keyword' => $request->input('keyword'),
                'category_ids' => $this->convertToArray($request->input('category', [])), 
                'source_ids' => $this->convertToArray($request->input('source', [])), 
                'date' => $request->input('date'),
                'authors' => [], 
                'page' => $request->input('page')
            ];

            $preferences = optional($user->preferences)->settings;
        
            if ($preferences) {

                $filters['source_ids'] = array_unique(array_merge(
                    $filters['source_ids'],
                    (array) ($preferences['source_ids'] ?? [])
                ));

                $filters['category_ids'] = array_unique(array_merge(
                    $filters['category_ids'],
                    (array) ($preferences['category_ids'] ?? [])
                ));

                $filters['authors'] = array_unique(array_merge(
                    $filters['authors'],
                    (array) ($preferences['authors'] ?? [])
                ));
            }

            $articles = $this->articleService->getArticles($filters);

            return response()->json(['message' => 'Articles listed successfully', 'articles' => $articles]);

        } catch (\Throwable $e) {
            Log::error('Failed to fetch articles: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to fetch articles', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($articleId)
    {
        try {
            $article = $this->articleService->getArticleById($articleId);

            return response()->json(['message' => 'Article listed successfully', 'article' => $article]);

        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to fetch article', 'message' => $e->getMessage()], 500);
        }
    }

    
    private function convertToArray($input)
    {
        if (is_string($input)) {
            return explode(',', $input);
        }
        
        return (array) $input;
    }
}
