<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Toutes les routes web chargent la même vue app.blade.php
| Vue Router prend ensuite le relais pour gérer la navigation côté client
|
*/

// Toutes les routes (/, /login, /dashboard, etc.) chargent la vue app.blade.php
// Vue Router gère ensuite la navigation
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
