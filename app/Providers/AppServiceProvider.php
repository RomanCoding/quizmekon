<?php

namespace App\Providers;

use App\Rules\YoutubeVideo;
use App\Services\Youtube;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Services\Youtube', function() {
            return new Youtube();
        });

    }
}
