<?php
// List all reports in database
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $reports = \App\Models\Report::all();
    echo "📋 Reports in Database:\n";
    echo str_repeat("-", 50) . "\n";
    foreach ($reports as $report) {
        echo "ID: {$report->id}\n";
        echo "Name: {$report->name}\n";
        echo "Code: {$report->code}\n";
        echo "Source: {$report->source_name}\n";
        echo str_repeat("-", 50) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
