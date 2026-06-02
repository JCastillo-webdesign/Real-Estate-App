<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider
 * 
 * The application service provider class.
 * Use this to register bindings, event listeners, and other application services.
 * This provider is automatically called during the application bootstrap process.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * 
     * Bindings and dependency injection can be registered here.
     * This method is called before the boot() method.
     * 
     * @return void
     */
    public function register(): void
    {
        // Register application services here
        // Example: $this->app->singleton(Service::class, Implementation::class);
    }

    /**
     * Bootstrap any application services.
     * 
     * Application logic that depends on other services can be registered here.
     * This method is called after all service providers have been registered.
     * 
     * @return void
     */
    public function boot(): void
    {
        // Bootstrap application services here
        // Example: View::share('key', 'value');
    }
}
