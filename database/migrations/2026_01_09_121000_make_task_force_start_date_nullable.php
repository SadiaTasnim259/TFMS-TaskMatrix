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
        Schema::table('task_forces', function (Blueprint $table) {
            $table->date('start_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_forces', function (Blueprint $table) {
            // Revert to NOT NULL
            // Note: This might fail if there are existing null values
            $table->date('start_date')->nullable(false)->change();
        });
    }
};
