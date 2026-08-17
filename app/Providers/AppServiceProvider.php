<?php

namespace App\Providers;

use App\Livewire\Classroom\JoinClassroom;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Component;
use Livewire\Features\SupportRedirects\SupportRedirects;

use function Livewire\on;

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

        // Fire newly-unlocked achievements to the browser right after a Livewire
        // update instead of waiting for the next full page load.
        on('dehydrate', function ($component, $context) {
            if (! $component instanceof Component || ! method_exists($context, 'addEffect')) {
                return;
            }

            // Initial page mounts use the layout x-init bootstrap instead
            if (method_exists($context, 'isMounting') && $context->isMounting()) {
                return;
            }

            // Redirect responses can't run JS effects — leave the session for the next page load
            if ($component instanceof JoinClassroom || SupportRedirects::$atLeastOneMountedComponentHasRedirected) {
                return;
            }

            $unlocks = session()->pull('new_achievements', []);
            if ($unlocks === []) {
                return;
            }

            $payload = json_encode($unlocks, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            $context->addEffect('xjs', [[
                'expression' => 'window.dispatchEvent(new CustomEvent("achievement-unlocked", { detail: '.$payload.' }))',
                'params' => [],
            ]]);
        });
    }
}
