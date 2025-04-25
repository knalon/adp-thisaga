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
        Schema::table('cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cars', 'is_sold')) {
                $table->boolean('is_sold')->default(false)->after('is_approved');
            }
            
            if (!Schema::hasColumn('cars', 'sold_at')) {
                $table->timestamp('sold_at')->nullable()->after('is_sold');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'is_sold',
                'sold_at',
            ]);
        });
    }
}; 