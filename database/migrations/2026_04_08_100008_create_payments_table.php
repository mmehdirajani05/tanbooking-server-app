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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('type'); // payment, refund, withdrawal
            $table->string('method'); // card, mobile_money, bank_transfer, cash
            $table->string('status'); // pending, success, failed, reversed
            $table->string('transaction_reference')->nullable();
            $table->string('gateway_response')->nullable();
            $table->json('gateway_metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('transaction_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
