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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('unit_type_id')->nullable()->constrained('unit_types')->nullOnDelete();
            $table->string('product_code', 100)->nullable();
            $table->string('name');
            $table->string('bahan')->nullable();
            $table->string('gramatur')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('ukuran_potongan_1')->nullable();
            $table->string('ukuran_plano_1')->nullable();
            $table->string('ukuran_potongan_2')->nullable();
            $table->string('ukuran_plano_2')->nullable();
            $table->decimal('buying_price', 20, 2)->nullable();
            $table->decimal('selling_price', 20, 2)->nullable();
            $table->decimal('quantity', 20, 2)->default(0);
            $table->decimal('reorder_level', 20, 2)->default(0);
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('keterangan_tambahan')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
