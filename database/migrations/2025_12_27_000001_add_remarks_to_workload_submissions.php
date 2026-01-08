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
        Schema::table('workload_submissions', function (Blueprint $table) {
            $table->text('lecturer_remarks')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workload_submissions', function (Blueprint $table) {
            $table->dropColumn('lecturer_remarks');
        });
    }
};
