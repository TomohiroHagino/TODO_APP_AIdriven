<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // TodoRepositoryのバインディング
        $this->app->bind(
            \App\Domain\Todo\Repository\TodoRepositoryInterface::class,
            \App\Infrastructure\Todo\Repository\TodoRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
