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
        Schema::create('stock_movement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('reference_type', ['purchase_order', 'sales_order', 'adjustment'])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('movement_type', ['in', 'out'])->nullable();
            $table->decimal('quantity', 20, 2)->nullable();
            $table->decimal('balance_after', 20, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movement');
    }
};
