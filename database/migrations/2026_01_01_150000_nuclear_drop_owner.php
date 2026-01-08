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
        $table = 'task_forces';
        $column = 'owner_id';

        // 1. Dynamic FK Discovery and Drop
        $dbName = config('database.connections.mysql.database');

        // Find constraint name from information_schema
        $fks = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = ? 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$dbName, $table, $column]);

        foreach ($fks as $fk) {
            $constraintName = $fk->CONSTRAINT_NAME;
            try {
                DB::statement("ALTER TABLE $table DROP FOREIGN KEY $constraintName");
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // 2. Disable Constraints and Drop Column
        Schema::disableForeignKeyConstraints();
        try {
            if (Schema::hasColumn($table, $column)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        } catch (\Exception $e) {
            // Fallback to raw SQL
            try {
                DB::statement("ALTER TABLE $table DROP COLUMN $column");
            } catch (\Exception $ex) {
            }
        }
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_forces', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable();
        });
    }
};
