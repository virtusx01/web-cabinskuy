<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth; // For the Auth facade
use Stevebauman\Location\Facades\Location; // For the Location facade
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
        if (!Auth::check() && !session()->has('locale')) {
        $ip = request()->ip(); // Get the user's IP address
        
        // Use a dummy IP for local testing if needed
        // $ip = '103.119.53.5'; // Example Indonesian IP

        if ($location = Location::get($ip)) {
            if ($location->countryCode === 'ID') {
                app()->setLocale('id');
                session()->put('locale', 'id');
            } else {
                app()->setLocale('en');
                session()->put('locale', 'en');
            }
        } else {
            // Fallback if IP detection fails
            app()->setLocale('en');
            session()->put('locale', 'en');
        }
    }
    }
    
}
