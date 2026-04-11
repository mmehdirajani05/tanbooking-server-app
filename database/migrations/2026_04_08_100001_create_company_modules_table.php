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
        Schema::create('company_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('module_type'); // hotel, tourism, event, esim
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'module_type']);
            $table->index(['module_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_modules');
    }
};
