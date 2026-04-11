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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category'); // music, party, culture, business, sports, etc.
            $table->string('subcategory')->nullable();
            $table->string('status')->default('draft'); // draft, pending, published, cancelled, completed
            $table->date('event_date');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('venue_name');
            $table->string('venue_type'); // beach, pool, hall, outdoor, indoor, etc.
            $table->string('city');
            $table->string('region');
            $table->text('address');
            $table->string('banner_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('video_url')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->string('itinerary_document')->nullable();
            $table->json('search_keywords')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['event_date', 'status']);
            $table->index(['city', 'event_date']);
            $table->index(['category', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
