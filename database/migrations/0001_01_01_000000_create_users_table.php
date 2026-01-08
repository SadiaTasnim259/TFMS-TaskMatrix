<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // User's full name
            $table->string('name');
            
            // Email (unique - no two users with same email)
            $table->string('email')->unique();
            
            // Email verification timestamp
            // NULL if not verified, timestamp if verified
            $table->timestamp('email_verified_at')->nullable();
            
            // Password (hashed with bcrypt)
            $table->string('password');
            
            // Remember me token (for "remember me" checkbox)
            $table->rememberToken();
            
            // Foreign key to roles table
            // Which role does this user have?
            $table->unsignedBigInteger('role_id')->nullable();
            
            // Is the account active?
            // false = account disabled, true = active
            $table->boolean('is_active')->default(true);
            
            // Count of failed login attempts
            // Used for account lockout mechanism
            $table->integer('failed_login_attempts')->default(0);
            
            // When the account gets unlocked
            // NULL = not locked, timestamp = locked until this time
            $table->timestamp('locked_until')->nullable();
            
            // Is this the user's first login?
            // true = must change password, false = can access normally
            $table->boolean('is_first_login')->default(true);
            
            // When did they last successfully log in?
            $table->timestamp('last_login_at')->nullable();
            
            // Standard Laravel timestamps: created_at, updated_at
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
