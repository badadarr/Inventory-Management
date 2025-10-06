<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('jenis_bahan')->nullable()->after('tanggal_kirim');
            $table->string('gramasi')->nullable()->after('jenis_bahan');
            $table->integer('volume')->nullable()->after('gramasi');
            $table->decimal('harga_jual_pcs', 20, 2)->nullable()->after('volume');
            $table->integer('jumlah_cetak')->nullable()->after('harga_jual_pcs');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['jenis_bahan', 'gramasi', 'volume', 'harga_jual_pcs', 'jumlah_cetak']);
        });
    }
};
