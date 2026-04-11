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
        // Update hotels table
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('owner_id')->constrained('companies')->onDelete('cascade');
            }
            if (!Schema::hasColumn('hotels', 'retail_price')) {
                $table->decimal('retail_price', 10, 2)->nullable()->after('status');
            }
            if (!Schema::hasColumn('hotels', 'contract_price')) {
                $table->decimal('contract_price', 10, 2)->nullable()->after('retail_price');
            }
            if (!Schema::hasColumn('hotels', 'search_tags')) {
                $table->json('search_tags')->nullable()->after('contract_price');
            }
            if (!Schema::hasColumn('hotels', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->nullable()->after('search_tags');
            }
            if (!Schema::hasColumn('hotels', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->nullable()->after('discount_percentage');
            }
        });

        // Update bookings table
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('customer_id')->constrained('companies')->nullOnDelete();
            }
            if (!Schema::hasColumn('bookings', 'module_type')) {
                $table->string('module_type')->default('hotel')->after('company_id');
            }
            if (!Schema::hasColumn('bookings', 'bookable_id')) {
                $table->unsignedBigInteger('bookable_id')->nullable()->after('room_type_id');
            }
            if (!Schema::hasColumn('bookings', 'bookable_type')) {
                $table->string('bookable_type')->nullable()->after('bookable_id');
            }
            if (!Schema::hasColumn('bookings', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->nullable()->after('total_price');
            }
            if (!Schema::hasColumn('bookings', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('paid_amount');
            }
            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('refund_amount');
            }
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('bookings', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('bookings', 'cancelled_by')) {
                $table->string('cancelled_by')->nullable()->after('cancelled_at');
            }
            if (!Schema::hasColumn('bookings', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'retail_price', 'contract_price', 'search_tags', 'discount_percentage', 'discount_amount']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id', 'module_type', 'bookable_id', 'bookable_type',
                'paid_amount', 'refund_amount', 'payment_status', 
                'payment_method', 'payment_reference', 'cancelled_by', 'cancellation_reason'
            ]);
        });
    }
};
