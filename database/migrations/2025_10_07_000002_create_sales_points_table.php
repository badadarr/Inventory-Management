<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('product_type', ['box', 'kertas_nasi_padang']);
            $table->integer('jumlah_cetak')->default(0);
            $table->decimal('points', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_points');
    }
};
