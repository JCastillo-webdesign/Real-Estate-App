<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Routes
 * 
 * All API routes are prefixed with '/api' and protected with rate limiting.
 * Use Sanctum middleware for token-based authentication.
 */

/**
 * Get authenticated user endpoint
 * 
 * Returns the authenticated user's information.
 * Requires a valid Sanctum authentication token.
 * 
 * GET /api/user
 */
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
