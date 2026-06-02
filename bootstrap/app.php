<?php

/**
 * Application Bootstrap
 * 
 * This file initializes and configures the entire Laravel application.
 * It sets up routing, middleware, and exception handling.
 */

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    // Configure application routes
    ->withRouting(
        web: __DIR__.'/../routes/web.php',          // Web routes for browser requests
        api: __DIR__.'/../routes/api.php',          // API routes
        commands: __DIR__.'/../routes/console.php', // CLI commands
        health: '/up',                              // Health check endpoint
    )
    // Configure middleware
    ->withMiddleware(function (Middleware $middleware) {
        // Add custom middleware here
        // Example: $middleware->alias('admin', \App\Http\Middleware\AdminMiddleware::class);
    })
    // Configure exception handling
    ->withExceptions(function (Exceptions $exceptions) {
        // Configure how exceptions are rendered and logged
    })->create();
