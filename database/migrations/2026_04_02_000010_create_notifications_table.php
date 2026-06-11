<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // e.g. "job_assigned", "job_accepted", "review_received"
            $table->string('type', 100);

            $table->string('title', 255);
            $table->text('body')->nullable();

            // Optional structured data (e.g. link to a request)
            // e.g. {"request_id": 42, "url": "/customer/requests/42"}
            $table->json('data')->nullable();

            // Null = unread, timestamp = when the user opened it
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Indexes for the notification bell queries
            $table->index('user_id');
            $table->index('read_at');
            $table->index(['user_id', 'read_at']); // Compound for unread count query
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
