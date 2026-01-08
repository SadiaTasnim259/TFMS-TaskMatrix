<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membership_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_force_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Staff being added/removed
            $table->string('action'); // 'add' or 'remove'
            $table->string('role')->nullable(); // Role in taskforce (e.g., Member, Secretary)
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('requested_by')->constrained('users'); // HOD who requested
            $table->foreignId('processed_by')->nullable()->constrained('users'); // PSM who processed
            $table->text('remarks')->nullable(); // Justification or rejection reason
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // Add 'is_locked' to task_forces if strictly required by UC to lock the *TaskForce* itself
        // OR rely on the fact that approved members are "locked" until exceptional modification.
        // Based on user's prompt "system will lock the task force", strict lock column is safer.
        Schema::table('task_forces', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_requests');

        Schema::table('task_forces', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
