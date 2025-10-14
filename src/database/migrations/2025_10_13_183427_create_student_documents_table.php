<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('tenant_id')->index();

            $table->enum('document_type', [
                'birth_certificate',
                'tc', // Transfer Certificate
                'id_proof',
                'photo',
                'medical',
                'caste',
                'income',
                'other'
            ]);
            $table->string('document_name');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->string('file_type')->nullable();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();

            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['student_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
