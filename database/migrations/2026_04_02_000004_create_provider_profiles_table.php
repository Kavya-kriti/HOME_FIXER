<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()                        // One profile per provider
                  ->constrained('users')
                  ->onDelete('cascade');            // Profile deleted when user is deleted
            $table->text('bio')->nullable();
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->unsignedInteger('service_radius_km')->default(10); // How far they travel
            $table->decimal('hourly_rate', 8, 2)->nullable();          // INR per hour
            $table->decimal('avg_rating', 3, 2)->default(0.00);        // 0.00 – 5.00
            $table->unsignedInteger('total_jobs')->default(0);         // Denormalized for AI scoring
            $table->boolean('is_available')->default(true);
            $table->string('id_proof_path')->nullable();               // Uploaded ID document
            $table->timestamp('verified_at')->nullable();              // Admin verification stamp
            $table->timestamps();

            $table->index('is_available');
            $table->index('avg_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
