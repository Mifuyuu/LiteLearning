<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Setup;
use App\Livewire\Dashboard;
use App\Livewire\Classroom\Index as ClassroomIndex;
use App\Livewire\Classroom\Show as ClassroomShow;
use App\Livewire\Classroom\People;
use App\Livewire\Assignment\Create as AssignmentCreate;
use App\Livewire\Assignment\Show as AssignmentShow;
use App\Livewire\Assignment\Grade;
use App\Livewire\Settings;
use App\Http\Controllers\SidebarClassroomPreferenceController;

// Landing page
Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user->needsSetup()
            ? redirect()->route('setup')
            : redirect()->route('dashboard');
    }
    return view('welcome');
})->name('landing');

Route::view('/tos', 'pages.tos')->name('tos');

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

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/setup', Setup::class)->name('setup');

    Route::middleware('setup')->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Gamification (Student only)
    Route::get('/store', \App\Livewire\Student\Store::class)->name('store');
    Route::get('/leaderboard', \App\Livewire\Student\Leaderboard::class)->name('leaderboard');
    Route::get('/achievements', \App\Livewire\Student\Achievements::class)->name('achievements');

    // Classrooms
    Route::get('/classrooms', ClassroomIndex::class)->name('classrooms');
    Route::get('/c/{classroom}', ClassroomShow::class)->name('classroom.show');
    Route::get('/c/{classroom}/people', People::class)->name('classroom.people');

    // Assignments
    Route::get('/c/{classroom}/a/create', AssignmentCreate::class)->name('assignment.create');
    Route::get('/c/{classroom}/a/{assignment}', AssignmentShow::class)->name('assignment.show');
    Route::get('/c/{classroom}/a/{assignment}/g/{submission}', Grade::class)->name('assignment.grade');

    // Calendar
    Route::get('/calendar', function () {
        return view('pages.calendar');
    })->name('calendar');

    // To Review
    Route::get('/to-review', function () {
        return view('pages.to-review');
    })->name('to-review');

    // Profile & Settings
    Route::get('/profile', function () {
        return view('pages.profile');
    })->name('profile');

    Route::get('/settings', Settings::class)->name('settings');

    // Bug Reports (Admin only)
    Route::get('/admin/reports', \App\Livewire\Admin\BugReports::class)
        ->name('admin.reports')
        ->middleware('admin');

    // Sidebar classroom preferences
    Route::post('/sidebar/classrooms/reorder', [SidebarClassroomPreferenceController::class, 'reorder'])
        ->name('sidebar.classrooms.reorder');
    });
});
