<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;

Route::get('/', function () {
    return redirect()->route('addresses.index');
});

Route::resource('addresses', AddressController::class);

