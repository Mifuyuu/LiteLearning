<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'teacher' => \App\Http\Middleware\EnsureUserIsTeacher::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'not_admin' => \App\Http\Middleware\EnsureUserIsNotAdmin::class,
            'student' => \App\Http\Middleware\EnsureUserIsStudent::class,
            'setup' => \App\Http\Middleware\EnsureUserHasCompletedSetup::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
