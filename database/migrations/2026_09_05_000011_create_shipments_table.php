<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique()->index();
            $table->foreignId('trip_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('draft')->index();
            $table->string('origin_country')->default('ID');
            $table->string('destination_country')->default('JP');
            $table->string('bagasian_reference')->nullable()->index();
            $table->string('packing_estimate_weight')->nullable();
            $table->string('packing_estimate_volume')->nullable();
            $table->unsignedBigInteger('packing_estimate_cost')->nullable();
            $table->timestamp('sent_to_bagasian_at')->nullable();
            $table->string('bagasian_pdf_path')->nullable();
            $table->text('wa_message')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
