<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Which completed job this review is for
            $table->foreignId('request_id')
                  ->constrained('service_requests')
                  ->onDelete('cascade');

            // Who wrote the review (customer reviewing provider, or vice versa)
            $table->foreignId('reviewer_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Who is being reviewed
            $table->foreignId('reviewee_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // 1 to 5 stars
            $table->tinyInteger('rating')->unsigned();

            $table->text('comment')->nullable();

            $table->timestamps();

            // One review per reviewer per job
            $table->unique(['request_id', 'reviewer_id']);

            // Indexes for calculating avg_rating on provider_profiles
            $table->index('reviewee_id');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
