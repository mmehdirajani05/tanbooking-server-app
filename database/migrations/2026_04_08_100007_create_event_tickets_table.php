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
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('name'); // VIP, Regular, Early Bird, etc.
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity_total');
            $table->unsignedInteger('quantity_available');
            $table->text('description')->nullable();
            $table->json('perks')->nullable(); // Array of VIP perks
            $table->dateTime('sale_start_date')->nullable();
            $table->dateTime('sale_end_date')->nullable();
            $table->string('status')->default('active'); // active, sold_out, inactive
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_tickets');
    }
};
