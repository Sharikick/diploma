<?php

namespace App\Providers;

use App\Parsers\StylesParser;
use App\Services\DocumentService;
use App\View\Components\Header;
use App\View\Components\Layout;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StylesParser::class);
        $this->app->bind(DocumentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component("header", Header::class);
        Blade::component("layout", Layout::class);
    }
}
