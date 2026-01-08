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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Action type (CREATE, UPDATE, DELETE, etc.)
            $table->string('action'); // CREATE, UPDATE, DELETE, DEACTIVATE, REACTIVATE, PASSWORD_RESET, ROLE_CHANGE, LOCK, UNLOCK
            
            // Entity information
            $table->string('model_type'); // Staff, TaskForce, Configuration, Department, User
            $table->unsignedBigInteger('model_id'); // ID of affected entity
            
            // User who performed action
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
            
            // Request information
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            // Change tracking (JSON)
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            
            // Description (for complex changes)
            $table->text('description')->nullable();
            
            // Metadata
            $table->string('model_name')->nullable(); // Display name (e.g., "Ali Ahmed" for staff)
            $table->string('status')->default('completed'); // completed, failed, pending
            
            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            // Indexes for performance
            $table->index('action');
            $table->index('model_type');
            $table->index('user_id');
            $table->index('created_at');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
