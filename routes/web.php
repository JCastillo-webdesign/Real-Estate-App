<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;

/**
 * Root path redirect
 * Routes the homepage to the addresses list
 */
Route::get('/', function () {
    return redirect()->route('addresses.index');
});

/**
 * Address resource routes
 * 
 * Provides RESTful endpoints for address management:
 * - GET    /addresses           - List all addresses
 * - GET    /addresses/create    - Show address creation form
 * - POST   /addresses           - Store new address
 * - GET    /addresses/{id}      - Show specific address
 * - GET    /addresses/{id}/edit - Show address edit form
 * - PUT    /addresses/{id}      - Update address
 * - DELETE /addresses/{id}      - Delete address
 */
Route::resource('addresses', AddressController::class);

