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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('workload_status')->default('Draft'); // Draft, Submitted, Approved, Rejected
            $table->timestamp('workload_submitted_at')->nullable();
            $table->boolean('workload_locked')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['workload_status', 'workload_submitted_at', 'workload_locked']);
        });
    }
};
