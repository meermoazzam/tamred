<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::macro('whereLike', function($column, $search) {
            return $this->where($column, 'LIKE', "%{$search}%");
        });

        Builder::macro('orWhereLike', function($column, $search) {
            return $this->orWhere($column, 'LIKE', "%{$search}%");
        });
    }
}
