<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_recommendation_logs', function (Blueprint $table) {
            $table->id();

            // Which service request triggered this AI call
            $table->foreignId('request_id')
                  ->constrained('service_requests')
                  ->onDelete('cascade');

            // Full JSON payload sent TO the Python script
            $table->json('input_payload');

            // Full JSON payload received FROM the Python script
            $table->json('output_payload')->nullable();

            // Track which version of your model produced this result
            $table->string('model_version', 50)->default('v1.0');

            // Performance monitoring
            $table->unsignedInteger('response_time_ms')->nullable();

            // Whether the AI call succeeded or failed
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();

            // No updated_at — logs are immutable
            $table->timestamp('created_at')->useCurrent();

            $table->index('request_id');
            $table->index('created_at');
            $table->index('model_version');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_recommendation_logs');
    }
};
