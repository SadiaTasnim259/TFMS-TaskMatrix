<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskForcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * PURPOSE: Create task_forces table for storing task force definitions
     *
     * Fields Explained:
     * - id: Primary key
     * - task_force_id: Unique identifier (e.g., TF001)
     * - name: Task force name
     * - category: Type of task force (Academic, Research, etc.)
     * - description: What this task force does
     * - default_weightage: Default workload units for members
     * - owner_id: Staff member who owns/chairs this task force
     * - start_date: When task force begins
     * - end_date: When task force ends
     * - active: Whether currently active
     * - created_by/updated_by: Track modifications
     * - timestamps: Track when records were modified
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_forces', function (Blueprint $table) {
            // Primary Keys and Foreign Keys
            $table->id();
            $table->unsignedBigInteger('owner_id')->comment('Task force owner/chair');

            // Task Force Identification
            $table->string('task_force_id', 50)->unique()->comment('Unique task force identifier');
            $table->string('name')->comment('Task force name');

            // Task Force Details
            $table->enum('category', [
                'Academic',
                'Research',
                'Accreditation',
                'Quality',
                'Strategic',
                'Administrative'
            ])->comment('Task force category');

            $table->text('description')->nullable()->comment('Task force description');

            // Weightage Configuration
            $table->decimal('default_weightage', 5, 2)->default(1.0)
                ->comment('Default workload units for this task force');

            // Dates
            $table->date('start_date')->comment('Task force start date');
            $table->date('end_date')->nullable()->comment('Task force end date');

            // Status
            $table->boolean('active')->default(true)->comment('Active status');

            // Audit Trail
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who created record');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User who updated record');

            // Timestamps
            $table->timestamps();

            // Foreign Keys
            $table->foreign('owner_id')
                ->references('id')
                ->on('staff')
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
            $table->index('task_force_id');
            $table->index('category');
            $table->index('active');
            $table->index('owner_id');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * PURPOSE: Drop task_forces table when rolling back
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_forces');
    }
}
