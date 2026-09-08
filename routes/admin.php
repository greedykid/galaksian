<?php

use App\Http\Controllers\Api\V1\Admin\AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\AdminBannerController;
use App\Http\Controllers\Api\V1\Admin\AdminBrandController;
use App\Http\Controllers\Api\V1\Admin\AdminCategoryController;
use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\AdminOrderController;
use App\Http\Controllers\Api\V1\Admin\AdminProductController;
use App\Http\Controllers\Api\V1\Admin\AdminRefundController;
use App\Http\Controllers\Api\V1\Admin\AdminShipmentController;
use App\Http\Controllers\Api\V1\Admin\AdminTripController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

// Public admin auth
Route::post('/auth/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');

// Protected admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Products
    Route::apiResource('products', AdminProductController::class);
    Route::post('/products/import', [AdminProductController::class, 'import']);

    // Brands, Categories, Banners
    Route::apiResource('brands', AdminBrandController::class);
    Route::apiResource('categories', AdminCategoryController::class);
    Route::apiResource('banners', AdminBannerController::class);

    // Trips
    Route::apiResource('trips', AdminTripController::class);

    // Shipments
    Route::apiResource('shipments', AdminShipmentController::class);
    Route::post('/shipments/{id}/send-bagasian', [AdminShipmentController::class, 'sendBagasian']);

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
    Route::post('/orders/{id}/assign-shipment', [AdminOrderController::class, 'assignShipment']);
    Route::post('/orders/{id}/invoices', [AdminOrderController::class, 'createInvoice']);

    // Refunds
    Route::get('/refunds', [AdminRefundController::class, 'index']);
    Route::post('/refunds', [AdminRefundController::class, 'store']);
    Route::post('/refunds/{id}/approve', [AdminRefundController::class, 'approve']);
    Route::post('/refunds/{id}/reject', [AdminRefundController::class, 'reject']);

    // Users
    Route::apiResource('users', AdminUserController::class)->only(['index', 'show', 'update']);
});
