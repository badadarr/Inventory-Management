<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('tanggal_po')->nullable()->after('order_number');
            $table->date('tanggal_kirim')->nullable()->after('tanggal_po');
            $table->decimal('charge', 20, 2)->nullable()->after('discount_total');
            $table->text('catatan')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tanggal_po', 'tanggal_kirim', 'charge', 'catatan']);
        });
    }
};
