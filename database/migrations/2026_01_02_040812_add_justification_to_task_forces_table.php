<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('task_forces', function (Blueprint $table) {
            $table->text('justification')->nullable()->after('is_locked')->comment('Reason for unlocking the task force');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('task_forces', function (Blueprint $table) {
            $table->dropColumn('justification');
        });
    }
};
