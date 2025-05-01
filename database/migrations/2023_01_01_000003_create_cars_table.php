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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('color');
            $table->string('transmission')->default('Automatic');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->integer('mileage')->nullable();
            $table->string('fuel_type')->default('Petrol');
            $table->string('condition')->default('Used');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('approval_status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_sold')->default(false);
            $table->string('status')->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
