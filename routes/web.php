<?php

use App\Livewire\Assignment\Create as AssignmentCreate;
use App\Livewire\Assignment\Grade;
use App\Livewire\Assignment\Show as AssignmentShow;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Setup;
use App\Livewire\Classroom\Index as ClassroomIndex;
use App\Livewire\Classroom\People;
use App\Livewire\Classroom\Show as ClassroomShow;
use App\Livewire\Dashboard;
use App\Livewire\Material\Create as MaterialCreate;
use App\Livewire\Material\Show as MaterialShow;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user->needsSetup()
            ? redirect()->route('setup')
            : ($user->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('dashboard'));
    }

    return view('welcome');
})->name('landing');

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Logout
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

// ─── Authenticated routes ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Setup (OTP registration guarantees email_verified_at is always set)
    Route::get('/setup', Setup::class)->name('setup');

    Route::middleware('setup')->group(function () {

        // Dashboard (not for admin)
        Route::middleware('not_admin')->group(function () {
            Route::get('/dashboard', Dashboard::class)->name('dashboard');
        });

        // Gamification (Student only)
        Route::middleware('student')->group(function () {
            Route::get('/store', \App\Livewire\Student\Store::class)->name('store');
            Route::get('/leaderboard', \App\Livewire\Student\Leaderboard::class)->name('leaderboard');
            Route::get('/achievements', \App\Livewire\Student\Achievements::class)->name('achievements');
        });

        // Classrooms (not for admin)
        Route::middleware('not_admin')->group(function () {
            Route::get('/classrooms', ClassroomIndex::class)->name('classrooms');
            Route::get('/c/{classroom}', ClassroomShow::class)->name('classroom.show');
            Route::get('/c/{classroom}/people', People::class)->name('classroom.people');
        });

        // Assignments — create route MUST come before {assignment} wildcard
        Route::middleware('teacher')->group(function () {
            Route::get('/c/{classroom}/a/create', AssignmentCreate::class)->name('assignment.create');
            Route::get('/c/{classroom}/m/create', MaterialCreate::class)->name('material.create');
            Route::get('/c/{classroom}/a/{assignment}/g/{submission}', Grade::class)->name('assignment.grade');
        });
        Route::get('/c/{classroom}/a/{assignment}', AssignmentShow::class)->name('assignment.show');
        Route::get('/c/{classroom}/m/{material}', MaterialShow::class)->name('material.show');

        // Calendar & To-Review
        Route::view('/calendar', 'pages.calendar')->name('calendar');
        Route::view('/to-review', 'pages.to-review')->name('to-review');

        // Profile & Settings
        Route::get('/profile', \App\Livewire\Profile::class)->name('profile');
        Route::get('/settings', Settings::class)->name('settings');

        // Admin Routes
        Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
            Route::get('/users', \App\Livewire\Admin\Users::class)->name('users');
            Route::get('/classrooms', \App\Livewire\Admin\Classrooms::class)->name('classrooms');
            Route::get('/store', \App\Livewire\Admin\Store::class)->name('store');
            Route::get('/achievements', \App\Livewire\Admin\Achievements::class)->name('achievements');
            Route::get('/reports', \App\Livewire\Admin\BugReports::class)->name('reports');
            Route::get('/theme-categories', \App\Livewire\Admin\ThemeCategories::class)->name('theme-categories');
        });

    }); // setup

}); // auth
