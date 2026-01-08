<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create Role Table First (Moved from 2025_12_06_133018_create_roles_table.php)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            // Primary key
            $table->id();

            // User's full name
            $table->string('name');

            // Email (unique - no two users with same email)
            $table->string('email')->unique();

            // Email verification timestamp
            $table->timestamp('email_verified_at')->nullable();

            // Password (hashed with bcrypt)
            $table->string('password');

            // Remember me token
            $table->rememberToken();

            // Foreign key to roles table (Defined INLINE for SQLite compatibility)
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');

            // Is the account active?
            $table->boolean('is_active')->default(true);

            // Count of failed login attempts
            $table->integer('failed_login_attempts')->default(0);

            // When the account gets unlocked
            $table->timestamp('locked_until')->nullable();

            // Is this the user's first login?
            $table->boolean('is_first_login')->default(true);

            // When did they last successfully log in?
            $table->timestamp('last_login_at')->nullable();

            // Standard Laravel timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
