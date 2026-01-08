<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table 1: Workload Submissions
        Schema::create('workload_submissions', function (Blueprint $table) {
            $table->id();
            
            // Staff who submitted
            $table->unsignedBigInteger('staff_id')->index();
            
            // Submission metadata
            $table->string('academic_year')->default('2024/2025'); // e.g., "2024/2025"
            $table->enum('semester', ['1', '2'])->default('1'); // Semester 1 or 2
            $table->decimal('total_hours', 8, 2)->default(0); // Sum of all hours
            $table->decimal('total_credits', 8, 2)->default(0); // Sum of all credits
            
            // Status tracking
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->text('notes')->nullable(); // Admin/HOD comments
            
            // Approval tracking
            $table->unsignedBigInteger('submitted_by')->nullable()->index(); // User who submitted
            $table->unsignedBigInteger('approved_by')->nullable()->index(); // User who approved
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['staff_id', 'academic_year', 'semester']);
            $table->index('status');
        });

        // Table 2: Workload Items (individual activities)
        Schema::create('workload_items', function (Blueprint $table) {
            $table->id();
            
            // Link to submission
            $table->unsignedBigInteger('workload_submission_id')->index();
            
            // Activity details
            $table->enum('activity_type', [
                'teaching',
                'research',
                'admin',
                'student_support',
                'committee_work',
                'course_development',
                'marking_assessment'
            ]); // Type of activity
            $table->string('activity_name'); // e.g., "CS101 Lecture"
            $table->text('description')->nullable();
            
            // Metrics
            $table->decimal('hours_allocated', 8, 2); // Hours for this activity
            $table->decimal('credits_value', 8, 2)->default(1); // Credit weighting
            $table->integer('student_count')->nullable(); // For teaching activities
            
            // Additional info
            $table->string('course_code')->nullable(); // For teaching
            $table->integer('semester')->nullable();
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign key
            $table->foreign('workload_submission_id')
                ->references('id')
                ->on('workload_submissions')
                ->onDelete('cascade');
            
            // Indexes
            $table->index('activity_type');
        });

        // Table 3: Performance Scores
        Schema::create('performance_scores', function (Blueprint $table) {
            $table->id();
            
            // Who is being scored
            $table->unsignedBigInteger('staff_id')->index();
            
            // Scoring period
            $table->string('academic_year'); // e.g., "2024/2025"
            $table->enum('semester', ['1', '2', 'annual'])->default('annual');
            
            // Score components (0-100 scale)
            $table->decimal('teaching_score', 5, 2)->default(0);
            $table->decimal('research_score', 5, 2)->default(0);
            $table->decimal('admin_score', 5, 2)->default(0);
            $table->decimal('student_support_score', 5, 2)->default(0);
            
            // Overall score (weighted average)
            $table->decimal('overall_score', 5, 2)->default(0);
            
            // Weightages applied
            $table->decimal('teaching_weight', 5, 2)->default(40);
            $table->decimal('research_weight', 5, 2)->default(30);
            $table->decimal('admin_weight', 5, 2)->default(20);
            $table->decimal('support_weight', 5, 2)->default(10);
            
            // Rating category
            $table->enum('rating', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'unrated'])->default('unrated');
            
            // Comments
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('evaluated_by')->nullable()->index();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->foreign('evaluated_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique index (one score per staff per period)
            $table->unique(['staff_id', 'academic_year', 'semester']);
            
            // Indexes
            $table->index('rating');
            $table->index('overall_score');
        });

        // Table 4: Reports (generated reports metadata)
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            
            // Report details
            $table->enum('report_type', [
                'staff_workload',
                'department_workload',
                'performance_evaluation',
                'task_force_performance',
                'analytics_snapshot',
                'institutional_summary'
            ]);
            
            // What the report covers
            $table->string('academic_year');
            $table->enum('semester', ['1', '2', 'annual'])->default('annual');
            $table->unsignedBigInteger('staff_id')->nullable()->index(); // If for single staff
            $table->unsignedBigInteger('department_id')->nullable()->index(); // If for single dept
            $table->unsignedBigInteger('task_force_id')->nullable()->index(); // If for task force
            
            // Report file
            $table->string('file_path')->nullable(); // Path to saved file
            $table->string('file_name')->nullable(); // Name of file
            $table->enum('file_format', ['pdf', 'excel', 'html'])->default('pdf');
            
            // Generation info
            $table->unsignedBigInteger('generated_by')->index();
            $table->timestamp('generated_at');
            
            // Statistics
            $table->integer('total_records')->default(0);
            $table->text('summary')->nullable(); // Summary of report content
            
            // Timestamps
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('task_force_id')->references('id')->on('task_forces')->onDelete('set null');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('report_type');
            $table->index('generated_at');
        });

        // Table 5: Analytics Snapshots (dashboard metrics)
        Schema::create('analytics_snapshots', function (Blueprint $table) {
            $table->id();
            
            // Time period
            $table->date('snapshot_date');
            $table->time('snapshot_time');
            
            // Counts
            $table->integer('total_submissions')->default(0);
            $table->integer('completed_submissions')->default(0);
            $table->integer('pending_approvals')->default(0);
            
            // Workload metrics
            $table->decimal('average_workload_hours', 8, 2)->default(0);
            $table->decimal('max_workload_hours', 8, 2)->default(0);
            $table->decimal('min_workload_hours', 8, 2)->default(0);
            
            // Activity breakdown
            $table->integer('teaching_activities')->default(0);
            $table->integer('research_activities')->default(0);
            $table->integer('admin_activities')->default(0);
            
            // Performance metrics
            $table->decimal('average_performance_score', 5, 2)->default(0);
            $table->integer('high_performers')->default(0); // Score > 80
            $table->integer('low_performers')->default(0);  // Score < 50
            
            // Department stats
            $table->integer('participating_departments')->default(0);
            $table->integer('participating_staff')->default(0);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('snapshot_date');
            $table->unique('snapshot_date'); // One snapshot per day
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_snapshots');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('performance_scores');
        Schema::dropIfExists('workload_items');
        Schema::dropIfExists('workload_submissions');
    }
};
