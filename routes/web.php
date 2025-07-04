<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Pages\Admin\AdminDashboard;
use Illuminate\Support\Facades\Auth;


// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login');
    Route::get('/login', Login::class);
});

// Protected routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
});
