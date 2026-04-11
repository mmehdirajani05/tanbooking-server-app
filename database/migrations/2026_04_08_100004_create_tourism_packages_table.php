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
        Schema::create('tourism_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('title');
            $table->json('category'); // Multi-select: cultural, wildlife, marine, adventure, etc.
            $table->string('thumbnail')->nullable();
            $table->json('gallery')->nullable();
            $table->string('tour_type'); // private, group, shared
            $table->string('country')->default('Tanzania');
            $table->string('region'); // Arusha, Dar es Salaam, Zanzibar, etc.
            $table->json('locations')->nullable(); // Array of locations with map pointers
            $table->text('short_description');
            $table->longText('full_description');
            $table->json('highlights')->nullable();
            $table->text('unique_selling_points')->nullable();
            $table->integer('duration_days')->default(1);
            $table->decimal('price_adult', 10, 2)->default(0);
            $table->decimal('price_child', 10, 2)->nullable();
            $table->decimal('price_infant', 10, 2)->nullable();
            $table->decimal('group_price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->json('inclusions')->nullable();
            $table->json('exclusions')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('refund_rules')->nullable();
            $table->text('child_policy')->nullable();
            $table->json('safety_guidelines')->nullable();
            $table->json('requirements')->nullable();
            $table->json('search_keywords')->nullable();
            $table->string('status')->default('draft'); // draft, pending, approved, published
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['region', 'status']);
            $table->index(['tour_type', 'status']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourism_packages');
    }
};
