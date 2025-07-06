<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Pages\Admin\AdminDashboard;
use App\Livewire\Pages\Admin\Academics;
use App\Livewire\Pages\Admin\Campuses;
use App\Livewire\Pages\Admin\Courses;
use App\Livewire\Pages\Admin\ProgramHeads;
use App\Livewire\Pages\Admin\Registrars;
use App\Livewire\Pages\Registrar\RegistrarDashboard;
use Illuminate\Support\Facades\Auth;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login');
    Route::get('/login', Login::class);
});

// Protected routes
Route::middleware('auth')->group(function () {

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/academics', Academics::class)->name('academics');
        Route::get('/campuses', Campuses::class)->name('campuses');
        Route::get('/courses', Courses::class)->name('courses');
        Route::get('/program-heads', ProgramHeads::class)->name('program-heads');
        Route::get('/registrars', Registrars::class)->name('registrars');
    });

    // Registrar routes
    Route::middleware('role:registrar')->prefix('registrar')->name('registrar.')->group(function () {
        Route::get('/dashboard', RegistrarDashboard::class)->name('dashboard');
        Route::get('/students', \App\Livewire\Pages\Registrar\Students::class)->name('students');
    });

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});