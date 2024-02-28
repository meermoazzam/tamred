<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::enforceMorphMap([
            'comment' => 'App\Models\Comment',
            'post' => 'App\Models\Post',
            'user' => 'App\Models\User',
            'message' => 'App\Models\Chat\Message',
        ]);
    }
}
