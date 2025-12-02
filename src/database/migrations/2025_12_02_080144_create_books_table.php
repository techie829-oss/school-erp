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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('isbn')->nullable();
            $table->string('title');
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('edition')->nullable();
            $table->integer('copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('language')->default('English');
            $table->year('publication_year')->nullable();
            $table->string('rack_number')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->enum('status', ['available', 'unavailable', 'lost', 'damaged'])->default('available');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('book_categories')->onDelete('set null');
            
            $table->index(['tenant_id', 'title']);
            $table->index(['tenant_id', 'author']);
            $table->index(['tenant_id', 'status']);
            $table->index('isbn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
