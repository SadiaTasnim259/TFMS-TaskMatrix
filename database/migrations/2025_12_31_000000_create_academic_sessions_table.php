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
        if (!Schema::hasTable('academic_sessions')) {
            Schema::create('academic_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('academic_year'); // e.g., "2024/2025"
                $table->integer('semester'); // 1 or 2
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status')->default('planning'); // 'published', 'planning', 'archived'
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_sessions');
    }
};
