<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
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
        // Set Carbon locale to match app locale (Thai by default)
        Carbon::setLocale(config('app.locale'));

        $appUrl = config('app.url');
        $forceHttps = (bool) config('app.force_https', false);

        if (is_string($appUrl) && $appUrl !== '') {
            URL::forceRootUrl($appUrl);
        }

        if ($forceHttps) {
            URL::forceScheme('https');
        }
    }
}
