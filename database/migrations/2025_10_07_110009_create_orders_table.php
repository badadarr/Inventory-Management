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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('sales_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('order_number', 100)->unique();
            $table->decimal('sub_total', 20, 2)->nullable();
            $table->decimal('tax_total', 20, 2)->nullable();
            $table->decimal('discount_total', 20, 2)->nullable();
            $table->decimal('total', 20, 2)->nullable();
            $table->decimal('paid', 20, 2)->nullable();
            $table->decimal('due', 20, 2)->nullable();
            $table->decimal('charge', 20, 2)->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->date('tanggal_po')->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->string('jenis_bahan')->nullable();
            $table->string('gramasi')->nullable();
            $table->integer('volume')->nullable();
            $table->decimal('harga_jual_pcs', 20, 2)->nullable();
            $table->integer('jumlah_cetak')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
