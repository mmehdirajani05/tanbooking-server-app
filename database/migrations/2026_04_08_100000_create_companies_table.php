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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name'); // Legal name
            $table->string('display_name'); // Display name for public
            $table->string('business_type')->nullable(); // Company, Individual, Partnership, NGO
            $table->string('registration_number')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('license_number')->nullable();
            $table->date('incorporation_date')->nullable();
            $table->string('country')->default('Tanzania');
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable(); // {instagram, facebook, twitter, linkedin}
            $table->string('status')->default('pending'); // pending, approved, rejected, suspended
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['country', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
