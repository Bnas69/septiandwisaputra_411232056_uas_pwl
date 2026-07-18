<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'qris', 'transfer'])->default('cash')->after('grand_total');
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('paid')->after('payment_method');
            $table->string('payment_ref', 100)->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'payment_ref']);
        });
    }
};
