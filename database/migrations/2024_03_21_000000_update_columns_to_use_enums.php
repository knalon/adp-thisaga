<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('transmission')->change();
            $table->string('fuel_type')->change();
            $table->string('status')->default('available')->change();
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('status')->default('scheduled')->change();
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('transmission')->change();
            $table->string('fuel_type')->change();
            $table->string('status')->change();
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('status')->change();
        });
    }
}; 