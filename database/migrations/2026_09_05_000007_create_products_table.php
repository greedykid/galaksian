<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable()->unique();
            $table->string('slug')->unique();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('origin_country')->default('ID')->index();
            $table->string('currency')->default('IDR');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('discount_price')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->nullable()->default(5);
            $table->string('availability_type')->default('ready_stock')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_flash_sale')->default(false)->index();
            $table->timestamp('flash_sale_start_at')->nullable()->index();
            $table->timestamp('flash_sale_end_at')->nullable()->index();
            $table->integer('weight_gram')->nullable();
            $table->integer('length_cm')->nullable();
            $table->integer('width_cm')->nullable();
            $table->integer('height_cm')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
