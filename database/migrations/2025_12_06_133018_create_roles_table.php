<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // UP: Create the roles table when migration runs
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            // Primary key (auto-incrementing)
            $table->id();
            
            // Role name (unique - no duplicates)
            // Examples: Admin, HOD, Lecturer, PSM
            $table->string('name')->unique();

            // Role slug (unique - machine readable)
            // Examples: admin, hod, lecturer, psm
            $table->string('slug')->unique();
            
            // Optional description
            // Example: "Head of Department"
            $table->text('description')->nullable();
            
            // Timestamps: created_at and updated_at
            // Laravel auto-manages these
            $table->timestamps();
        });

        // Add foreign key constraint to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('set null');
        });
    }

    // DOWN: Drop the table when migration is rolled back
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
        Schema::dropIfExists('roles');
    }
};
