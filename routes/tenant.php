<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\TeamController;
use App\Http\Controllers\Tenant\ActivityLogController;
use App\Http\Controllers\Tenant\BillingController;

// Avatar route — tenancy already initialized hoti hai
// storage_path() automatically tenant folder include karta hai
Route::get('/avatar/{path}', function (string $path) {
    // Tenancy init hone ke baad storage_path('app/public/...') 
    // automatically = storage/tenant{id}/app/public/...
    $filePath = storage_path('app/public/' . $path);

    if (!file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath, [
        'Content-Type' => mime_content_type($filePath)
    ]);
})->where('path', '.*')->name('tenant.avatar');

Route::middleware(['auth', 'tenant.active'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

    Route::get('/profile',          [ProfileController::class, 'index'])->name('tenant.profile.index');
    Route::put('/profile/update',   [ProfileController::class, 'update'])->name('tenant.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('tenant.profile.password');
    Route::post('/profile/avatar',  [ProfileController::class, 'updateAvatar'])->name('tenant.profile.avatar');

    Route::get('/team',             [TeamController::class, 'index'])->name('tenant.team.index');
    Route::post('/team/invite',     [TeamController::class, 'invite'])->name('tenant.team.invite');
    Route::put('/team/{user}/role', [TeamController::class, 'updateRole'])->name('tenant.team.role');
    Route::delete('/team/{user}',   [TeamController::class, 'remove'])->name('tenant.team.remove');

    Route::get('/activity', [ActivityLogController::class, 'index'])->name('tenant.activity.index');

    Route::get('/billing',                  [BillingController::class, 'index'])->name('tenant.billing.index');
    Route::post('/billing/stripe-checkout', [BillingController::class, 'stripeCheckout'])->name('tenant.billing.stripe');
    Route::post('/billing/razorpay-order',  [BillingController::class, 'razorpayCreateOrder'])->name('tenant.billing.razorpay.order');
    Route::post('/billing/razorpay-verify', [BillingController::class, 'razorpayVerify'])->name('tenant.billing.razorpay.verify');
    Route::post('/billing/cancel',          [BillingController::class, 'cancel'])->name('tenant.billing.cancel');
});
