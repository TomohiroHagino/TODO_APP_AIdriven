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
        // UserRepositoryのバインディング
        $this->app->bind(
            \App\Domain\UserAggregate\Repository\UserRepositoryInterface::class,
            \App\Infrastructure\UserAggregate\Repository\UserRepository::class
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
