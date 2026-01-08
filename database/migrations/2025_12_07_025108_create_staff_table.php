<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * PURPOSE: Create staff table for storing all staff member details
     *
     * Fields Explained:
     * - id: Primary key
     * - user_id: Links to users table (for login)
     * - staff_id: Unique identifier (e.g., CS001)
     * - first_name: Staff first name
     * - last_name: Staff last name
     * - email: Email address (must be unique)
     * - department_id: Which department they belong to
     * - grade: Position level (Professor, Assoc. Prof, Senior Lecturer, Lecturer)
     * - employment_status: Employment type (Permanent, Contract, Visiting, Inactive)
     * - Role flags: is_hod, is_task_force_chair, is_program_coordinator
     * - active: Whether this staff record is active
     * - notes: Any additional notes
     * - created_by/updated_by: Track who created/updated
     * - timestamps: Track when records were modified
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            // Primary Keys and Foreign Keys
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->unique()->comment('Links to users table');
            $table->unsignedBigInteger('department_id')->comment('Department assignment');

            // Basic Information
            $table->string('staff_id', 50)->unique()->comment('Unique staff identifier');
            $table->string('first_name')->comment('First name');
            $table->string('last_name')->comment('Last name');
            $table->string('email')->unique()->comment('Email address');

            // Employment Details
            $table->enum('grade', [
                'Professor',
                'Assoc_Prof',
                'Senior_Lecturer',
                'Lecturer'
            ])->comment('Academic grade/position');

            $table->enum('employment_status', [
                'Permanent',
                'Contract',
                'Visiting',
                'Inactive'
            ])->default('Permanent')->comment('Employment type');

            // Role Flags
            $table->boolean('is_hod')->default(false)->comment('Is Head of Department');
            $table->boolean('is_task_force_chair')->default(false)->comment('Is Task Force Chair');
            $table->boolean('is_program_coordinator')->default(false)->comment('Is Program Coordinator');

            // Status and Notes
            $table->boolean('active')->default(true)->comment('Active status');
            $table->text('notes')->nullable()->comment('Additional notes');

            // Audit Trail
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who created record');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User who updated record');

            // Timestamps
            $table->timestamps();

            // Foreign Keys
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('restrict');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes for Performance
            $table->index('staff_id');
            $table->index('department_id');
            $table->index('active');
            $table->index('employment_status');
            $table->index('email');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * PURPOSE: Drop staff table when rolling back
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff');
    }
}
