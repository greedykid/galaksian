<?php

use App\Http\Controllers\Api\V1\User\AddressController;
use App\Http\Controllers\Api\V1\User\AuthController;
use App\Http\Controllers\Api\V1\User\BrandController;
use App\Http\Controllers\Api\V1\User\CartController;
use App\Http\Controllers\Api\V1\User\CategoryController;
use App\Http\Controllers\Api\V1\User\CheckoutController;
use App\Http\Controllers\Api\V1\User\HomeController;
use App\Http\Controllers\Api\V1\User\OrderController;
use App\Http\Controllers\Api\V1\User\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['localize'])->group(function () {
    // Auth endpoints
    Route::prefix('auth')->group(function () {
        Route::post('/otp/request', [AuthController::class, 'requestOtp']);
        Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    // Profile me
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // Home
    Route::get('/home', [HomeController::class, 'index']);

    // Catalog: products, brands, categories
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{slug}/products', [ProductController::class, 'brandProducts']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}/products', [ProductController::class, 'categoryProducts']);

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::patch('/items/{id}', [CartController::class, 'updateItem']);
        Route::delete('/items/{id}', [CartController::class, 'removeItem']);
        Route::post('/voucher', [CartController::class, 'applyVoucher']);
    });

    // Protected User routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/checkout', [CheckoutController::class, 'checkout']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/invoices/{invoiceId}/pay', [OrderController::class, 'payInvoice']);
        Route::get('/orders/{id}/invoices/{invoiceId}/download', [OrderController::class, 'downloadInvoice']);
        Route::post('/orders/{id}/review', [OrderController::class, 'review']);
        Route::post('/orders/{id}/items/{itemId}/resolve-oos', [OrderController::class, 'resolveOosItem']);

        Route::apiResource('addresses', AddressController::class);
    });

    // Admin API
    Route::prefix('admin')->group(base_path('routes/admin.php'));

    // Webhook endpoints
    Route::prefix('webhooks')->group(base_path('routes/webhooks.php'));
});
