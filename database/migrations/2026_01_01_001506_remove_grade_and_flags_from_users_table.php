<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'grade')) {
                $table->dropColumn(['grade', 'is_hod', 'is_task_force_chair', 'is_program_coordinator']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('grade', ['Professor', 'Assoc_Prof', 'Senior_Lecturer', 'Lecturer'])->nullable();
            $table->boolean('is_hod')->default(false);
            $table->boolean('is_task_force_chair')->default(false);
            $table->boolean('is_program_coordinator')->default(false);
        });
    }
};
