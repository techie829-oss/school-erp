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
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('student_id');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->integer('renewal_count')->default(0);
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->decimal('paid_fine', 10, 2)->default(0);
            $table->text('issue_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->enum('status', ['issued', 'returned', 'overdue', 'lost'])->default('issued');
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'book_id']);
            $table->index(['tenant_id', 'status']);
            $table->index('due_date');
            $table->index('issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
