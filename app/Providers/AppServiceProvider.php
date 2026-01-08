<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Configuration;

use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        // Share current session with all views
        try {
            $currentSession = Configuration::where('config_key', 'current_session')->value('config_value') ?? '2024/2025';
        } catch (\Exception $e) {
            $currentSession = '2024/2025';
        }

        if (\Illuminate\Support\Str::contains(request()->url(), ['serveo.net', 'loca.lt', 'serveousercontent.com'])) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        View::share('currentSession', $currentSession);
    }
}
