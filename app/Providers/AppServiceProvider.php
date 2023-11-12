<?php

namespace App\Providers;

use App\Services\EmployeeAuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the employee auth service as a singleton
        $this->app->singleton('employeeauth', function ($app) {
            return new EmployeeAuthService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
