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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('type', ['bug', 'feature_request', 'support', 'general'])->default('general');
            $table->string('tenant_id')->nullable(); // For tenant-wise filtering
            $table->unsignedBigInteger('created_by'); // Admin user who created the ticket
            $table->unsignedBigInteger('assigned_to')->nullable(); // Admin user assigned to handle
            $table->timestamp('due_date')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('admin_users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('admin_users')->onDelete('set null');
            $table->index(['tenant_id', 'status']);
            $table->index(['created_by']);
            $table->index(['assigned_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
