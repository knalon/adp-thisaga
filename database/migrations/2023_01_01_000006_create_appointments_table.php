<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->string('status')->default('scheduled');
            $table->boolean('is_test_drive')->default(true);
            $table->boolean('is_purchase_appointment')->default(false);
            $table->timestamps();
        });

        DB::statement("
            CREATE OR REPLACE VIEW appointment_details AS
            SELECT
                a.*,
                b.amount as bid_price
            FROM appointments a
            LEFT JOIN bids b ON a.bid_id = b.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS appointment_details");
        Schema::dropIfExists('appointments');
    }
};
