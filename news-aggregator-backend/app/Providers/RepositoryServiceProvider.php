<?php

namespace App\Providers;

use App\Repositories\{ArticleRepository, PreferenceRepository, UserRepository};
use App\Repositories\Contracts\{ArticleRepositoryInterface, PreferenceRepositoryInterface, UserRepositoryInterface};
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(PreferenceRepositoryInterface::class, PreferenceRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
