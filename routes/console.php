<?php

/**
 * Console Routes
 * 
 * Define custom Artisan commands that can be run from the command line.
 * These commands are only registered in CLI mode, not in HTTP requests.
 */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/**
 * Inspire Command
 * 
 * Displays an inspiring quote to the user.
 * Can be run with: php artisan inspire
 * 
 * @return void
 */
Artisan::command('inspire', function () {
    // Display an inspiring quote
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
