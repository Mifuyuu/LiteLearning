<?php

use App\Livewire\Assignment\Create as AssignmentCreate;
use App\Livewire\Assignment\Grade;
use App\Livewire\Assignment\Show as AssignmentShow;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Classroom\GradeReport;
use App\Livewire\Classroom\Index as ClassroomIndex;
use App\Livewire\Classroom\People;
use App\Livewire\Classroom\Settings as ClassroomSettings;
use App\Livewire\Classroom\Show as ClassroomShow;
use App\Livewire\Classroom\Stream as ClassroomStream;
use App\Livewire\Classroom\Work as ClassroomWork;
use App\Livewire\Dashboard;
use App\Livewire\Material\Create as MaterialCreate;
use App\Livewire\Material\Show as MaterialShow;
use App\Livewire\Settings;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('dashboard');
    }

    return view('welcome');
})->name('landing');

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
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

    // Dashboard (not for admin)
    Route::middleware('role:not_admin')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
    });

    // Gamification (Student only)
    Route::middleware('role:student')->group(function () {
        Route::get('/store', \App\Livewire\Student\Store::class)->name('store');
        Route::get('/inventory', \App\Livewire\Student\Inventory::class)->name('inventory');
        Route::get('/leaderboard', \App\Livewire\Student\Leaderboard::class)->name('leaderboard');
        Route::get('/achievements', \App\Livewire\Student\Achievements::class)->name('achievements');
    });

    // Classrooms (not for admin)
    Route::middleware('role:not_admin')->group(function () {
        Route::get('/classrooms', ClassroomIndex::class)->name('classrooms');
        Route::get('/c/{classroom}', ClassroomShow::class)->name('classroom.show');
        Route::get('/c/{classroom}/stream', ClassroomStream::class)->name('classroom.stream');
        Route::get('/c/{classroom}/settings', ClassroomSettings::class)->name('classroom.settings');
        Route::get('/w/{classroom}/t/{scope?}', ClassroomWork::class)->name('classroom.work');
        Route::get('/r/{classroom}/{sort?}', People::class)->name('classroom.roster');
        Route::get('/c/{classroom}/people', function (Classroom $classroom) {
            return redirect()->route('classroom.roster', [
                'classroom' => $classroom,
                'sort' => 'sort-first-name',
            ]);
        })->name('classroom.people');
    });

    // Assignments — create route MUST come before {assignment} wildcard
    Route::middleware('role:teacher')->group(function () {
        Route::get('/c/{classroom}/a/create', AssignmentCreate::class)->name('assignment.create');
        Route::get('/c/{classroom}/m/create', MaterialCreate::class)->name('material.create');
        Route::get('/c/{classroom}/a/{assignment}/g/{submission}', Grade::class)->name('assignment.grade');
        Route::get('/c/{classroom}/gb', GradeReport::class)->name('classroom.gradebook');
    });
    Route::get('/c/{classroom}/a/{assignment}', AssignmentShow::class)->name('assignment.show');
    Route::get('/c/{classroom}/m/{material}', MaterialShow::class)->name('material.show');

    // Calendar & To-Review
    Route::get('/calendar', \App\Livewire\Student\Calendar::class)->middleware('role:student')->name('calendar');
    Route::get('/to-review', \App\Livewire\ToReview::class)->middleware('role:teacher')->name('to-review');

    // Profile & Settings
    Route::get('/profile/{user?}', \App\Livewire\Profile::class)->name('profile');
    Route::get('/settings', Settings::class)->name('settings');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
        Route::get('/users', \App\Livewire\Admin\Users::class)->name('users');
        Route::get('/classrooms', \App\Livewire\Admin\Classrooms::class)->name('classrooms');
        Route::get('/store', \App\Livewire\Admin\Store::class)->name('store');
        Route::get('/achievements', \App\Livewire\Admin\Achievements::class)->name('achievements');
        Route::get('/reports', \App\Livewire\Admin\BugReports::class)->name('reports');
        Route::get('/theme-categories', \App\Livewire\Admin\ThemeCategories::class)->name('theme-categories');
    });

}); // auth
