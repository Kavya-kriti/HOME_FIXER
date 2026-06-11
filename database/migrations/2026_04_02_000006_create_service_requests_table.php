<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();

            // Who is requesting
            $table->foreignId('customer_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // What service is being requested (nullable — AI may suggest the service)
            $table->foreignId('service_id')
                  ->nullable()
                  ->constrained('services')
                  ->onDelete('set null');

            // Problem description (fed to AI)
            $table->string('title', 255);
            $table->text('description');

            // Location details
            $table->string('address');
            $table->decimal('latitude', 10, 7)->nullable();  // For distance-based AI scoring
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pincode', 10)->nullable();

            // Budget range (INR)
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();

            // Scheduling
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time')->nullable();

            // Job lifecycle status
            $table->enum('status', [
                'pending',      // Submitted, awaiting AI recommendation
                'recommended',  // AI has returned results, shown to customer
                'assigned',     // Customer accepted a provider
                'in_progress',  // Provider started the job
                'completed',    // Job done
                'cancelled',    // Cancelled by customer or admin
            ])->default('pending');

            // Stores raw AI input/output for display and logging
            $table->json('ai_recommendation_payload')->nullable();

            $table->timestamps();

            // Indexes for dashboard queries
            $table->index('customer_id');
            $table->index('service_id');
            $table->index('status');
            $table->index('preferred_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
