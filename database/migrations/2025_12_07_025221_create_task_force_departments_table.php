<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskForceDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * PURPOSE: Create junction table for task_force <-> department many-to-many relationship
     *
     * Why Junction Table?
     * - One task force can belong to multiple departments
     * - One department can have multiple task forces
     * - Need middle table to track these relationships
     *
     * Fields Explained:
     * - id: Primary key
     * - task_force_id: Foreign key to task_forces table
     * - department_id: Foreign key to departments table
     * - assigned_by: User who made this assignment
     * - assigned_at: When this assignment was made
     *
     * Example:
     * Task Force "Curriculum Development" assigned to:
     * - Computer Science
     * - Information Systems
     * - Data Science
     *
     * Would create 3 rows in this table
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_force_departments', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->unsignedBigInteger('task_force_id')->comment('Task force ID');
            $table->unsignedBigInteger('department_id')->comment('Department ID');

            // Audit
            $table->unsignedBigInteger('assigned_by')->nullable()->comment('User who assigned');

            // Timestamp
            $table->timestamp('assigned_at')->useCurrent()->comment('When assigned');

            // Foreign Key Constraints
            $table->foreign('task_force_id')
                ->references('id')
                ->on('task_forces')
                ->onDelete('cascade'); // If TF deleted, remove assignments

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade'); // If department deleted, remove assignments

            $table->foreign('assigned_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Unique Constraint - Cannot assign same TF to same department twice
            $table->unique(['task_force_id', 'department_id']);

            // Indexes
            $table->index('task_force_id');
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * PURPOSE: Drop task_force_departments table when rolling back
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_force_departments');
    }
}
