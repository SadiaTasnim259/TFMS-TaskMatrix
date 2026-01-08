<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Attempting to create academic_sessions table...\n";

if (Schema::hasTable('academic_sessions')) {
    echo "Table ALREADY EXISTS. Dropping it...\n";
    Schema::drop('academic_sessions');
}

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

if (Schema::hasTable('academic_sessions')) {
    echo "SUCCESS: academic_sessions table created.\n";
} else {
    echo "FAILURE: Could not create table.\n";
}
