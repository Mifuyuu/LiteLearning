<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Prefer authenticated user's saved locale, fall back to session, then config default
        if (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
        } else {
            $locale = session('locale', config('app.locale'));
        }

        if (in_array($locale, ['en', 'th'])) {
            App::setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
