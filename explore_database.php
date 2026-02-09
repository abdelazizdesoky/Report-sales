<?php
// Explore Views and Sample Data
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Exploring Views in aarabia2014...\n\n";
    
    // Get all views
    $views = DB::connection('sqlsrv')
        ->select("SELECT 
                    SCHEMA_NAME(schema_id) as SchemaName,
                    name as ViewName
                  FROM sys.views 
                  ORDER BY name");
    
    echo "📊 Available Views:\n";
    echo str_repeat("=", 80) . "\n";
    
    foreach ($views as $index => $view) {
        echo ($index + 1) . ". [{$view->SchemaName}].[{$view->ViewName}]\n";
    }
    echo "\n";
    
    // Explore Alarabia_AGING_SUMMARY view
    if (!empty($views)) {
        $viewName = 'Alarabia_AGING_SUMMARY';
        echo "🔍 Exploring View: {$viewName}\n";
        echo str_repeat("=", 80) . "\n\n";
        
        // Get columns
        echo "📋 Columns:\n";
        echo str_repeat("-", 80) . "\n";
        $columns = DB::connection('sqlsrv')
            ->select("SELECT 
                        COLUMN_NAME, 
                        DATA_TYPE, 
                        CHARACTER_MAXIMUM_LENGTH,
                        IS_NULLABLE
                      FROM INFORMATION_SCHEMA.COLUMNS 
                      WHERE TABLE_NAME = '{$viewName}'
                      ORDER BY ORDINAL_POSITION");
        
        printf("%-5s %-30s %-20s %-10s\n", "#", "Column Name", "Data Type", "Nullable");
        echo str_repeat("-", 80) . "\n";
        foreach ($columns as $index => $col) {
            $type = $col->DATA_TYPE;
            if ($col->CHARACTER_MAXIMUM_LENGTH) {
                $type .= "({$col->CHARACTER_MAXIMUM_LENGTH})";
            }
            printf("%-5s %-30s %-20s %-10s\n", 
                ($index + 1), 
                $col->COLUMN_NAME, 
                $type,
                $col->IS_NULLABLE
            );
        }
        
        // Get row count
        echo "\n";
        $count = DB::connection('sqlsrv')
            ->select("SELECT COUNT(*) as cnt FROM [{$viewName}]")[0]->cnt;
        echo "📊 Total Records: " . number_format($count) . "\n\n";
        
        // Get sample data
        echo "📄 Sample Data (First 5 Records):\n";
        echo str_repeat("=", 120) . "\n";
        
        $samples = DB::connection('sqlsrv')
            ->select("SELECT TOP 5 * FROM [{$viewName}]");
        
        if (!empty($samples)) {
            // Print header
            $firstRow = (array)$samples[0];
            foreach (array_keys($firstRow) as $colName) {
                printf("%-25s ", substr($colName, 0, 24));
            }
            echo "\n" . str_repeat("-", 120) . "\n";
            
            // Print data
            foreach ($samples as $row) {
                foreach ((array)$row as $value) {
                    $displayValue = $value;
                    if (is_string($value)) {
                        $displayValue = substr($value, 0, 24);
                    } elseif ($value instanceof DateTime) {
                        $displayValue = $value->format('Y-m-d');
                    }
                    printf("%-25s ", $displayValue);
                }
                echo "\n";
            }
        }
        
        echo str_repeat("=", 120) . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
