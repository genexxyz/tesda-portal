<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Pages\Admin\AdminDashboard;
use App\Livewire\Pages\Admin\Academics;
use App\Livewire\Pages\Admin\AssessmentManagement;
use App\Livewire\Pages\Admin\Campuses;
use App\Livewire\Pages\Admin\Courses;
use App\Livewire\Pages\Admin\ProgramHeads;
use App\Livewire\Pages\Admin\QualificationTypes;
use App\Livewire\Pages\Admin\Registrars;
use App\Livewire\Pages\ProgramHead\ProgramHeadDashboard;
use App\Livewire\Pages\Registrar\RegistrarDashboard;
use Illuminate\Support\Facades\Auth;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login');
    Route::get('/login', Login::class);
});

// Protected routes
Route::middleware('auth')->group(function () {
    
    
    // Profile route accessible to all authenticated users
    Route::get('/user/profile', \App\Livewire\Pages\User\Profile::class)->name('profile');
    // Change password route
    Route::get('/user/change-password', \App\Livewire\Pages\User\ChangePassword::class)->name('change-password');

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/academics', Academics::class)->name('academics');
        Route::get('/campuses', Campuses::class)->name('campuses');
        Route::get('/courses', Courses::class)->name('courses');
        Route::get('/program-heads', ProgramHeads::class)->name('program-heads');
        Route::get('/qualification-types', QualificationTypes::class)->name('qualification-types');
        Route::get('/registrars', Registrars::class)->name('registrars');
        Route::get('/assessment-management', AssessmentManagement::class)->name('assessment-management');
        Route::get('/students', \App\Livewire\Pages\Admin\Students::class)->name('students');
    });

    // Registrar routes
    Route::middleware('role:registrar')->prefix('registrar')->name('registrar.')->group(function () {
        Route::get('/dashboard', RegistrarDashboard::class)->name('dashboard');
        Route::get('/students', \App\Livewire\Pages\Registrar\Students::class)->name('students');
    });

    //Program Head routes
    Route::middleware('role:program-head')->prefix('program-head')->name('program-head.')->group(function () {
        Route::get('/dashboard', ProgramHeadDashboard::class)->name('dashboard');
        Route::get('/students', \App\Livewire\Pages\ProgramHead\Students::class)->name('students');
        Route::get('/assessments', \App\Livewire\Pages\ProgramHead\Assessments::class)->name('assessments');
        Route::get('/assessments/{assessmentId}', \App\Livewire\Pages\ProgramHead\AssessmentDetails::class)->name('assessment-details');
        Route::get('/submit-results/{assessment}', \App\Livewire\Pages\ProgramHead\SubmitResults::class)->name('submit-results');
        Route::get('/view-results', \App\Livewire\Pages\ProgramHead\ViewResults::class)->name('view-results');
    });

    //TesdaFocal routes
    Route::middleware('role:tesda-focal')->prefix('tesda-focal')->name('tesda-focal.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Pages\TesdaFocal\TesdaFocalDashboard::class)->name('dashboard');
        Route::get('/view-results', \App\Livewire\Pages\TesdaFocal\ViewResults::class)->name('view-results');
        // Using modal for viewing assessment results instead of route
    });

    //Student routes
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Pages\Student\Dashboard::class)->name('dashboard');
        
    });

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});