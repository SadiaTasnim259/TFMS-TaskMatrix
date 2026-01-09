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
            $table->unsignedBigInteger('owner_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_forces', function (Blueprint $table) {
            // Cannot easily revert to NOT NULL without data cleanup, 
            // but for definition sake we try to change it back.
            // Assumption: all records have owner_id if reversing.
            $table->unsignedBigInteger('owner_id')->nullable(false)->change();
        });
    }
};
