<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Blade で {{ Format::price(...) }} と書けるようにする（表記の統一用）
        AliasLoader::getInstance()->alias('Format', \App\Support\Format::class);
    }
}
