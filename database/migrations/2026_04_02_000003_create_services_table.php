<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('service_categories')
                  ->onDelete('restrict'); // Don't delete a category that has services
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2)->default(0.00); // Minimum/starting price (INR)
            $table->decimal('duration_estimate_hrs', 5, 2)->nullable(); // e.g. 1.5 = 1h 30m
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
