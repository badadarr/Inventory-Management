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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('nama_box')->nullable()->after('name');
            $table->string('nama_sales')->nullable()->after('nama_box');
            $table->string('nama_owner')->nullable()->after('nama_sales');
            $table->string('bulan_join')->nullable()->after('address');
            $table->string('tahun_join')->nullable()->after('bulan_join');
            $table->string('status_customer')->default('new')->after('tahun_join');
            $table->string('status_komisi')->nullable()->after('status_customer');
            $table->decimal('harga_komisi_standar', 20, 2)->nullable()->after('status_komisi');
            $table->decimal('harga_komisi_ekstra', 20, 2)->nullable()->after('harga_komisi_standar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'nama_box',
                'nama_sales',
                'nama_owner',
                'bulan_join',
                'tahun_join',
                'status_customer',
                'status_komisi',
                'harga_komisi_standar',
                'harga_komisi_ekstra'
            ]);
        });
    }
};
