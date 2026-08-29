<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\ScientificScore;
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
       View::composer('backend.pages.abstract-reviewer.partials.review-guidelines-modal', function ($view) {
            $view->with('criteria', ScientificScore::orderBy('id')->get());
        });
    }
}
