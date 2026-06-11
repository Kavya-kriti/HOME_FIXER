<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            // This creates the missing 'type' column
            $table->string('type')->default('ai')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};