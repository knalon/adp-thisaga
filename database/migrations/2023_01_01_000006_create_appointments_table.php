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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('bid_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('appointment_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->boolean('is_test_drive')->default(true);
            $table->boolean('is_purchase_appointment')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
}; 