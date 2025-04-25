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
        Schema::table('transactions', function (Blueprint $table) {
            // Rename final_price to amount if it exists
            if (Schema::hasColumn('transactions', 'final_price')) {
                $table->renameColumn('final_price', 'amount');
            } else if (!Schema::hasColumn('transactions', 'amount')) {
                $table->decimal('amount', 10, 2)->after('appointment_id');
            }
            
            // Add payment fields if they don't exist
            if (!Schema::hasColumn('transactions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('transactions', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('transactions', 'payment_date')) {
                $table->timestamp('payment_date')->nullable()->after('transaction_id');
            }
            
            if (!Schema::hasColumn('transactions', 'notes')) {
                $table->text('notes')->nullable()->after('payment_date');
            }
            
            if (!Schema::hasColumn('transactions', 'receipt')) {
                $table->string('receipt')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('transactions', 'shipping_status')) {
                $table->string('shipping_status')->nullable()->after('receipt');
            }
            
            if (!Schema::hasColumn('transactions', 'shipping_date')) {
                $table->timestamp('shipping_date')->nullable()->after('shipping_status');
            }
            
            if (!Schema::hasColumn('transactions', 'shipping_notes')) {
                $table->text('shipping_notes')->nullable()->after('shipping_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // This is tricky because we don't know if final_price or amount was the original
            // For simplicity, we'll assume amount is the current name and revert to final_price
            if (Schema::hasColumn('transactions', 'amount')) {
                $table->renameColumn('amount', 'final_price');
            }
            
            $table->dropColumn([
                'payment_method',
                'transaction_id',
                'payment_date',
                'notes',
                'receipt',
                'shipping_status',
                'shipping_date',
                'shipping_notes',
            ]);
        });
    }
};