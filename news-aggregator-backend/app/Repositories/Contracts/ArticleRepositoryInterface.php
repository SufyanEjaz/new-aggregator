<?php

namespace App\Repositories\Contracts;

interface ArticleRepositoryInterface
{
    public function getFilteredArticles(array $filters = []);
    public function getArticleById(int $articleId);
}