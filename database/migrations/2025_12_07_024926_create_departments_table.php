<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * PURPOSE: Create departments table for organizing staff
     * 
     * Fields Explained:
     * - id: Primary key (auto-increment)
     * - name: Department name (e.g., "Computer Science")
     * - code: Unique department code (e.g., "CS")
     * - description: Department description
     * - head_id: Foreign key to staff (department head)
     * - active: Boolean to mark active/inactive departments
     * - timestamps: created_at, updated_at for tracking changes
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Department name');
            $table->string('code', 50)->unique()->comment('Unique department code');
            $table->text('description')->nullable()->comment('Department description');
            $table->unsignedBigInteger('head_id')->nullable()->comment('Department head (HOD)');
            $table->boolean('active')->default(true)->comment('Active status');
            $table->timestamps();

            // Note: Foreign key for head_id will be added after staff table is created
            // See: add_department_head_foreign_key migration

            // Indexes for frequently queried columns
            $table->index('active');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * PURPOSE: Drop departments table when rolling back
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
