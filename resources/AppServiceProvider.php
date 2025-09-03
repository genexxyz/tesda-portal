<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\School;

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
    $school = School::first();
    $schoolInfo = $school ?? [
        'name' => 'School Name',
        'address' => 'Address',
        'contact_number' => 'Contact',
        'email' => 'Email',
        'website' => 'Website',
        'logo' => 'Logo URL',
        'tagline' => 'Tagline',
    ];

    $this->app->singleton('schoolInfo', function () use ($schoolInfo) {
        return $schoolInfo;
    });
}
}
