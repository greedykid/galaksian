<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('user_id')->constrained('vouchers')->nullOnDelete();
            $table->boolean('is_gift')->default(false)->after('currency');
            $table->string('gift_from')->nullable()->after('is_gift');
            $table->string('gift_to')->nullable()->after('gift_from');
            $table->text('gift_message')->nullable()->after('gift_to');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('gift_fee_amount')->default(0)->after('handling_fee_amount');
            $table->boolean('is_gift')->default(false)->after('notes');
            $table->string('gift_from')->nullable()->after('is_gift');
            $table->string('gift_to')->nullable()->after('gift_from');
            $table->text('gift_message')->nullable()->after('gift_to');
            $table->boolean('has_insurance')->default(false)->after('gift_message');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['gift_fee_amount', 'is_gift', 'gift_from', 'gift_to', 'gift_message', 'has_insurance']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['voucher_id', 'is_gift', 'gift_from', 'gift_to', 'gift_message']);
        });
    }
};
