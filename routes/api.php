<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes — Tenant aware
| Tenancy initialization manually karo kyunki 'api' group mein
| InitializeTenancyByDomainEarly nahi hoti (sirf 'web' mein hai)
|--------------------------------------------------------------------------
*/

Route::middleware([
    \App\Http\Middleware\InitializeTenancyByDomainEarly::class,
])->group(function () {

    // Public
    Route::post('/auth/login', [ApiController::class, 'login']);

    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [ApiController::class, 'logout']);
        Route::get('/auth/me',      [ApiController::class, 'me']);
        Route::get('/users',        [ApiController::class, 'users']);
        Route::get('/tenant',       [ApiController::class, 'tenantInfo']);
    });

});