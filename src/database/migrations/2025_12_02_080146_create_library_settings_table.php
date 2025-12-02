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
        Schema::create('library_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->integer('max_books_per_student')->default(3);
            $table->integer('issue_duration_days')->default(14);
            $table->decimal('fine_per_day', 8, 2)->default(5.00);
            $table->integer('max_renewals')->default(2);
            $table->integer('renewal_duration_days')->default(7);
            $table->decimal('book_lost_fine', 10, 2)->nullable();
            $table->decimal('book_damage_fine', 10, 2)->nullable();
            $table->boolean('allow_online_issue')->default(false);
            $table->boolean('send_overdue_notifications')->default(true);
            $table->integer('overdue_notification_days')->default(1); // Days before due date to send notification
            $table->text('library_rules')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_settings');
    }
};
