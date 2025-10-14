<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_documents', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('teacher_id');

            $table->string('document_name');
            $table->enum('document_type', ['resume', 'certificate', 'experience_letter', 'id_proof', 'address_proof', 'photo', 'other']);
            $table->string('file_path');
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->string('mime_type', 100)->nullable();

            $table->timestamp('uploaded_at')->useCurrent();
            $table->unsignedBigInteger('uploaded_by')->nullable();

            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->index(['teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_documents');
    }
};

