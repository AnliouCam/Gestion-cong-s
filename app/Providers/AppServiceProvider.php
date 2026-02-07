<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        // Fix pour MySQL : limite la longueur des index à 191 caractères
        // Nécessaire pour les anciennes versions MySQL qui ont une limite de 1000 bytes pour les index
        Schema::defaultStringLength(191);
    }
}
