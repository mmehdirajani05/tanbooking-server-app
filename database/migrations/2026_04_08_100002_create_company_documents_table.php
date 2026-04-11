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
        Schema::create('company_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('module_type')->nullable(); // null = company-wide, or specific module
            $table->string('document_type'); // brela, tin, tourism_license, event_license, etc.
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['document_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_documents');
    }
};
