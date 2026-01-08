<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$output = "";

try {
    $tables = \Illuminate\Support\Facades\DB::select('SHOW CREATE TABLE task_forces');
    foreach ($tables as $table) {
        // cast to array to handle object property variability
        $t = (array) $table;
        $output .= "Table: " . ($t['Table'] ?? $t['table']) . "\n";
        $output .= "Create Table: \n" . ($t['Create Table'] ?? $t['create table']) . "\n";
    }

    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE task_forces');
    $output .= "\nColumns:\n";
    foreach ($columns as $col) {
        $output .= $col->Field . " | " . $col->Type . " | " . $col->Null . " | " . $col->Default . "\n";
    }
} catch (\Exception $e) {
    $output .= "Error: " . $e->getMessage();
}

file_put_contents('debug_output.txt', $output);
