<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('request_id')
                  ->constrained('service_requests')
                  ->onDelete('cascade');

            $table->foreignId('provider_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Assignment lifecycle timestamps
            $table->timestamp('assigned_at')->nullable();   // When customer/admin offered job
            $table->timestamp('accepted_at')->nullable();   // When provider accepted
            $table->timestamp('started_at')->nullable();    // When provider marked started
            $table->timestamp('completed_at')->nullable();  // When provider marked done

            // Provider's quoted price for this specific job
            $table->decimal('quoted_price', 10, 2)->nullable();

            // Notes from provider (e.g., "need to buy parts first")
            $table->text('provider_notes')->nullable();

            $table->enum('status', [
                'offered',   // Sent to provider, awaiting response
                'accepted',  // Provider confirmed
                'rejected',  // Provider declined
                'started',   // Work in progress
                'done',      // Provider marked complete
            ])->default('offered');

            $table->timestamps();

            // Indexes for provider dashboard queries
            $table->index('provider_id');
            $table->index('request_id');
            $table->index('status');

            // A provider can only be assigned to a request once at a time
            $table->unique(['request_id', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_assignments');
    }
};
