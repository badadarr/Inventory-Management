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
        Schema::create('order_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type'); // created, updated, status_changed, payment_added, cancelled, completed
            $table->string('description'); // Human readable description
            $table->json('old_values')->nullable(); // Store old values for updates
            $table->json('new_values')->nullable(); // Store new values for updates
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['order_id', 'created_at']);
            $table->index('activity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_activities');
    }
};
