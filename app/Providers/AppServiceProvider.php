<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $cacheKey = 'stats_data';

        User::created(function () use ($cacheKey) {
            Cache::forget($cacheKey);
        });

        User::updated(function () use ($cacheKey) {
            Cache::forget($cacheKey);
        });

        User::deleted(function () use ($cacheKey) {
            Cache::forget($cacheKey);
        });

        Post::created(function () use ($cacheKey) {
            Cache::forget($cacheKey);
        });

        Post::updated(function () use ($cacheKey) {
            Cache::forget($cacheKey);
        });

        Post::deleted(function () use ($cacheKey) {
            Cache::forget($cacheKey);
        });
    }
}
