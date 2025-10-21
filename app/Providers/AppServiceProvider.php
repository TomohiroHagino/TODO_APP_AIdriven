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
        // UserRepositoryのバインディング（新しいDDD構造）
        $this->app->bind(
            \App\Domain\UserAggregate\Repository\UserRepositoryInterface::class,
            \App\Infrastructure\UserAggregate\Repository\UserRepository::class
        );

        // TodoRepositoryのバインディング（旧構造 - 後で削除予定）
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
