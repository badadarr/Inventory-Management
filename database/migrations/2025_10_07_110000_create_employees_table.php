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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('phone', 50)->nullable();
            $table->string('designation')->nullable();
            $table->text('address')->nullable();
            $table->decimal('salary', 20, 2)->nullable();
            $table->string('photo')->nullable();
            $table->string('nid')->nullable();
            $table->enum('status', ['active', 'resigned'])->default('active');
            $table->date('joining_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
