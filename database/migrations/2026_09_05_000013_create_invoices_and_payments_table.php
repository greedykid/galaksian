<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique()->index();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('type')->index(); // product, shipping, additional
            $table->string('status')->default('pending')->index(); // pending, paid, expired, failed, refunded
            $table->unsignedBigInteger('amount');
            $table->text('description')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_reference')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('method');
            $table->string('status')->default('pending')->index(); // pending, paid, failed, expired, refund
            $table->unsignedBigInteger('amount');
            $table->string('gateway_reference')->nullable()->index();
            $table->json('raw_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
