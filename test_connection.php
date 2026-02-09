<?php
// Test SQL Server Connection - Try to connect to master database first
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing SQL Server Connection...\n\n";
echo "Connection Details:\n";
echo "Host: " . env('DB_SECONDARY_HOST') . "\n";
echo "Username: " . env('DB_SECONDARY_USERNAME') . "\n";
echo "Database: " . env('DB_SECONDARY_DATABASE') . "\n\n";

// Try different authentication methods
$attempts = [
    [
        'desc' => 'Using specified database (user)',
        'database' => env('DB_SECONDARY_DATABASE'),
    ],
    [
        'desc' => 'Using master database',
        'database' => 'master',
    ],
];

foreach ($attempts as $attempt) {
    echo "Attempt: {$attempt['desc']}\n";
    echo str_repeat("-", 50) . "\n";
    
    try {
        config(['database.connections.sqlsrv.database' => $attempt['database']]);
        DB::purge('sqlsrv');
        
        $pdo = DB::connection('sqlsrv')->getPdo();
        echo "✅ Connected successfully!\n\n";
        
        // List all databases
        echo "📊 Available Databases:\n";
        $databases = DB::connection('sqlsrv')
            ->select("SELECT name FROM sys.databases ORDER BY name");
        
        foreach ($databases as $index => $db) {
            echo ($index + 1) . ". " . $db->name . "\n";
        }
        
        echo "\n✅ Connection successful with database: {$attempt['database']}\n";
        break;
        
    } catch (\Exception $e) {
        echo "❌ Failed: " . $e->getMessage() . "\n\n";
    }
}
