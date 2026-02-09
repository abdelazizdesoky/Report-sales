<?php
// Script to verify Salesman Security Logic
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Report;
use App\Services\ReportService;

echo "🔐 Testing Salesman Security Logic...\n";
echo "----------------------------------------\n";

// 1. Get or Create a Test User with Salesman Name
$testEmail = 'test_salesman@example.com';
$user = User::firstOrCreate(
    ['email' => $testEmail],
    [
        'name' => 'Test Salesman User',
        'password' => bcrypt('password'),
        'salesman_name' => 'Ahmed Ali' // Example Salesman
    ]
);

echo "👤 User: {$user->name}\n";
echo "🔗 Assigned Salesman: {$user->salesman_name}\n";
echo "----------------------------------------\n";

// 2. Mock Auth
auth()->login($user);

// 3. Test Report Query
try {
    $report = Report::where('code', 'aging_report')->firstOrFail();
    $service = new ReportService();

    // Use reflection to access private/protected method if needed, 
    // but here we test the public method getAgingReportData which uses getBaseQuery
    $data = $service->getAgingReportData($report, [], 1, 1);
    
    // Check the query logic (we can't easily see the SQL, but we can check results if DB connects)
    // If DB connects, it will return data filtered by 'Ahmed Ali'
    echo "✅ Query executed successfully.\n";
    echo "📊 Rows returned: " . $data->count() . "\n";
    
    if ($data->count() > 0) {
        $firstRow = $data->first();
        echo "🔍 First Row Salesman: " . $firstRow->SalesMan . "\n";
        if ($firstRow->SalesMan == $user->salesman_name) {
            echo "✅ SECURITY CHECK PASSED: Returned data matches assigned salesman.\n";
        } else {
            echo "❌ SECURITY CHECK FAILED: Returned data has different salesman!\n";
        }
    } else {
        echo "⚠️ No data found for this salesman (or connection failed).\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Cleanup
// $user->delete(); // Keep for manual testing if needed
