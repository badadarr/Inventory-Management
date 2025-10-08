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
        Schema::table('products', function (Blueprint $table) {
            $table->string('bahan')->nullable()->after('description');
            $table->string('gramatur')->nullable()->after('bahan');
            $table->string('ukuran')->nullable()->after('gramatur');
            $table->string('ukuran_potongan_1')->nullable()->after('ukuran');
            $table->string('ukuran_plano_1')->nullable()->after('ukuran_potongan_1');
            $table->string('ukuran_potongan_2')->nullable()->after('ukuran_plano_1');
            $table->string('ukuran_plano_2')->nullable()->after('ukuran_potongan_2');
            $table->text('alamat_pengiriman')->nullable()->after('ukuran_plano_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'bahan',
                'gramatur',
                'ukuran',
                'ukuran_potongan_1',
                'ukuran_plano_1',
                'ukuran_potongan_2',
                'ukuran_plano_2',
                'alamat_pengiriman'
            ]);
        });
    }
};
