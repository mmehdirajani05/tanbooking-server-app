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
        Schema::create('tour_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('tourism_packages')->onDelete('cascade');
            $table->integer('day_number');
            $table->string('time_slot')->nullable(); // e.g., "06:00 AM - 09:00 AM"
            $table->string('activity_title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->json('included_meals')->nullable(); // ["breakfast", "lunch", "dinner"]
            $table->json('transport_details')->nullable(); // {vehicle_type, pickup_location, pickup_time}
            $table->timestamps();

            $table->index('package_id');
            $table->index(['package_id', 'day_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_itineraries');
    }
};
