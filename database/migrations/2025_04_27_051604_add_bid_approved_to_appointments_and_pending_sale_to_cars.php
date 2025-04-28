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
        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('bid_approved')->default(false)->after('bid_price');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->boolean('is_pending_sale')->default(false)->after('is_sold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('bid_approved');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('is_pending_sale');
        });
    }
};
