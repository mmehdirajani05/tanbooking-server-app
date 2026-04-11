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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('module_type')->nullable(); // hotel, tourism, event, or null for all
            $table->unsignedBigInteger('module_id')->nullable(); // Specific hotel, package, event ID
            $table->string('type'); // percentage, fixed_amount
            $table->decimal('value', 10, 2);
            $table->string('code')->nullable()->unique(); // Promo code
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('min_booking_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->string('status')->default('active'); // active, inactive, expired
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['code', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
