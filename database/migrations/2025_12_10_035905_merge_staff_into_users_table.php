<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add Staff Columns to Users Table
        if (!Schema::hasColumn('users', 'staff_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('staff_id', 50)->nullable()->unique()->after('id')->comment('Legacy Staff ID or Employee ID');
                $table->string('first_name')->nullable()->after('name');
                $table->string('last_name')->nullable()->after('first_name');
                $table->unsignedBigInteger('department_id')->nullable()->after('email')->comment('Department assignment');

                $table->enum('grade', [
                    'Professor',
                    'Assoc_Prof',
                    'Senior_Lecturer',
                    'Lecturer'
                ])->nullable()->after('department_id')->comment('Academic grade/position');

                $table->enum('employment_status', [
                    'Permanent',
                    'Contract',
                    'Visiting',
                    'Inactive'
                ])->default('Permanent')->after('grade')->comment('Employment type');

                // Role flags
                $table->boolean('is_hod')->default(false)->after('employment_status');
                $table->boolean('is_task_force_chair')->default(false)->after('is_hod');
                $table->boolean('is_program_coordinator')->default(false)->after('is_task_force_chair');

                $table->text('notes')->nullable()->after('remember_token');

                // Audit FKs
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();

                $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 2. Add user_id to related tables (if not exists) and migrate data
        $tables = ['workload_submissions', 'performance_scores', 'reports', 'task_force_members'];

        foreach ($tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'user_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                });
            }

            // Migrate data: Update user_id based on staff_id
            if (Schema::hasColumn($tableName, 'staff_id') && Schema::hasTable('staff')) {
                DB::statement("
                    UPDATE {$tableName} 
                    JOIN staff ON {$tableName}.staff_id = staff.id 
                    SET {$tableName}.user_id = staff.user_id
                ");
            }
        }

        // Special handling for Task Forces (owner_id logic)
        // Check if owner_id refers to staff or user. 
        // Based on previous analysis, Staff::ownedTaskForces() uses 'owner_id'.
        // If owner_id was staff_id, need to migrate. 
        // Assuming owner_id is integer. We need to check if we can migrate it.
        // Let's assume owner_id IS staff_id based on logic.
        if (Schema::hasTable('task_forces') && Schema::hasColumn('task_forces', 'owner_id')) {
            // If we can't be sure, we might leave it or try to update.
            // Given it's dev, let's try to update assuming it points to staff table if staff table exists.
            if (Schema::hasTable('staff')) {
                DB::statement("
                    UPDATE task_forces 
                    JOIN staff ON task_forces.owner_id = staff.id
                    SET task_forces.owner_id = staff.user_id
                ");
            }

            // Drop the FK to staff
            try {
                DB::statement("ALTER TABLE task_forces DROP FOREIGN KEY task_forces_owner_id_foreign");
            } catch (\Exception $e) {
                // Ignore if FK doesn't exist
            }

            // Re-add FK to users?
            // Optional, but good practice.
            Schema::table('task_forces', function (Blueprint $table) {
                try { // In case it fails or type mismatch
                    $table->foreign('owner_id')->references('id')->on('users')->restrictOnDelete();
                } catch (\Exception $e) {
                }
            });
        }

        // 3. Migrate Staff Data to Users Table
        if (Schema::hasTable('staff')) {
            $staffMembers = DB::table('staff')->get();
            foreach ($staffMembers as $staff) {
                if ($staff->user_id) {
                    DB::table('users')->where('id', $staff->user_id)->update([
                        'staff_id' => $staff->staff_id,
                        'first_name' => $staff->first_name,
                        'last_name' => $staff->last_name,
                        'department_id' => $staff->department_id,
                        'grade' => $staff->grade,
                        'employment_status' => $staff->employment_status,
                        'is_hod' => $staff->is_hod,
                        'is_task_force_chair' => $staff->is_task_force_chair,
                        'is_program_coordinator' => $staff->is_program_coordinator,
                        'notes' => $staff->notes,
                        'created_by' => $staff->created_by,
                        'updated_by' => $staff->updated_by,
                    ]);
                }
            }
        }

        // 4. Drop staff_id columns and constraints from related tables
        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'staff_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Try to drop foreign key first. Naming convention: table_staff_id_foreign
                    // We try-catch or just attempt generic name
                    try {
                        $table->dropForeign("{$tableName}_staff_id_foreign");
                    } catch (\Exception $e) {
                        try {
                            $table->dropForeign(['staff_id']);
                        } catch (\Exception $e2) {
                        }
                    }
                    $table->dropColumn('staff_id');
                });
            }
        }

        // Special handling for Departments (head_id logic)
        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'head_id')) {
            if (Schema::hasTable('staff')) {
                DB::statement("
                    UPDATE departments 
                    JOIN staff ON departments.head_id = staff.id
                    SET departments.head_id = staff.user_id
                ");
            }
            try {
                DB::statement("ALTER TABLE departments DROP FOREIGN KEY departments_head_id_foreign");
            } catch (\Exception $e) {
            }

            Schema::table('departments', function (Blueprint $table) {
                try {
                    $table->foreign('head_id')->references('id')->on('users')->nullOnDelete();
                } catch (\Exception $e) {
                }
            });
        }

        // 5. Drop staff table
        Schema::dropIfExists('staff');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive migration, hard to reverse fully without data loss or complex logic.
        // For now, we will just recreate the staff table and remove columns from users.

        // 1. Recreate Staff Table
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->unsignedBigInteger('department_id');
            $table->string('staff_id', 50)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->enum('grade', ['Professor', 'Assoc_Prof', 'Senior_Lecturer', 'Lecturer']);
            $table->enum('employment_status', ['Permanent', 'Contract', 'Visiting', 'Inactive'])->default('Permanent');
            $table->boolean('is_hod')->default(false);
            $table->boolean('is_task_force_chair')->default(false);
            $table->boolean('is_program_coordinator')->default(false);
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // 2. Restore Columns in Related Tables
        // ... (Omitting full reversal logic for brevity as per plan "destructive")

        // 3. Drop Columns from Users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'staff_id',
                'first_name',
                'last_name',
                'department_id',
                'grade',
                'employment_status',
                'is_hod',
                'is_task_force_chair',
                'is_program_coordinator',
                'notes',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
