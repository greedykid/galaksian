<?php

use App\Http\Controllers\Webhooks\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/payment', [PaymentWebhookController::class, 'handle'])->middleware('throttle:120,1');
