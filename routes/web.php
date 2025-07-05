<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Pages\Admin\AdminDashboard;
use App\Livewire\Pages\Admin\Academics;
use App\Livewire\Pages\Admin\Campuses;
use App\Livewire\Pages\Admin\Courses;
use App\Livewire\Pages\Admin\ProgramHeads;
use App\Livewire\Pages\Admin\Registrars;
use Illuminate\Support\Facades\Auth;


// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login');
    Route::get('/login', Login::class);
});

// Protected routes


Route::middleware('auth')->group(function () {

    //Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/academics', Academics::class)->name('admin.academics');
        Route::get('/campuses', Campuses::class)->name('admin.campuses');
        Route::get('/courses', Courses::class)->name('admin.courses');
        Route::get('/program-heads', ProgramHeads::class)->name('admin.program-heads');
        Route::get('/registrars', Registrars::class)->name('admin.registrars');
    });


    Route::post('/logout', function () {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
