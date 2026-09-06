<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending_payment_product')->index();
            $table->string('currency')->default('IDR');
            $table->unsignedBigInteger('exchange_rate')->nullable()->default(1);
            $table->json('address_snapshot');
            $table->unsignedBigInteger('product_subtotal');
            $table->unsignedBigInteger('product_discount_amount')->default(0);
            $table->unsignedBigInteger('new_user_discount_amount')->default(0);
            $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('voucher_amount')->default(0);
            $table->unsignedBigInteger('handling_fee_amount')->default(0);
            $table->unsignedBigInteger('product_total');
            $table->unsignedBigInteger('shipping_jastip_amount')->nullable();
            $table->unsignedBigInteger('shipping_local_amount')->nullable();
            $table->unsignedBigInteger('shipping_total')->nullable();
            $table->unsignedBigInteger('grand_total')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('product_paid_at')->nullable();
            $table->timestamp('shipping_paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('product_name_snapshot');
            $table->string('brand_name_snapshot')->nullable();
            $table->string('sku_snapshot')->nullable();
            $table->integer('qty');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('original_price');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('subtotal');
            $table->string('availability_type')->default('ready_stock');
            $table->string('refund_status')->nullable();
            $table->unsignedBigInteger('refund_amount')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
