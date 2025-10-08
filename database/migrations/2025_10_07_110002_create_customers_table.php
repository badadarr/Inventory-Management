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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('nama_box')->nullable();
            $table->string('nama_owner')->nullable();
            $table->string('bulan_join', 50)->nullable();
            $table->string('tahun_join', 50)->nullable();
            $table->enum('status_customer', ['baru', 'repeat'])->default('baru');
            $table->integer('repeat_order_count')->default(0);
            $table->string('status_komisi', 100)->nullable();
            $table->decimal('harga_komisi_standar', 20, 2)->nullable();
            $table->decimal('harga_komisi_extra', 20, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
