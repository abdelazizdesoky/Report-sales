<?php
// Discover the sales hierarchy view and how it relates to the aging report.
// Usage:  php explore_hierarchy.php  [optional_view_name]

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$conn = DB::connection('sqlsrv');

function printColumns($conn, string $table): array
{
    $columns = $conn->select(
        "SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_NAME = ?
         ORDER BY ORDINAL_POSITION",
        [$table]
    );

    if (empty($columns)) {
        echo "   !! not found\n";
        return [];
    }

    $names = [];
    foreach ($columns as $col) {
        $type = $col->DATA_TYPE . ($col->CHARACTER_MAXIMUM_LENGTH ? "({$col->CHARACTER_MAXIMUM_LENGTH})" : '');
        printf("   %-32s %s\n", $col->COLUMN_NAME, $type);
        $names[] = $col->COLUMN_NAME;
    }

    return $names;
}

function printSample($conn, string $table, int $rows = 3): void
{
    try {
        $samples = $conn->select("SELECT TOP {$rows} * FROM [{$table}]");
        foreach ($samples as $i => $row) {
            echo "   [" . ($i + 1) . "] ";
            $parts = [];
            foreach ((array) $row as $k => $v) {
                $parts[] = "{$k}=" . (is_null($v) ? 'NULL' : mb_substr((string) $v, 0, 30));
            }
            echo implode(' | ', $parts) . "\n";
        }
    } catch (\Exception $e) {
        echo "   !! sample failed: " . $e->getMessage() . "\n";
    }
}

try {
    echo str_repeat('=', 90) . "\n";
    echo "1) AGING REPORT VIEW: Alarabia_AGING_SUMMARY\n";
    echo str_repeat('=', 90) . "\n";
    $agingColumns = printColumns($conn, 'Alarabia_AGING_SUMMARY');

    echo "\n" . str_repeat('=', 90) . "\n";
    echo "2) CANDIDATE HIERARCHY TABLES/VIEWS\n";
    echo str_repeat('=', 90) . "\n";

    $target = $argv[1] ?? null;

    if ($target) {
        $candidates = [(object) ['TABLE_SCHEMA' => 'dbo', 'TABLE_NAME' => $target]];
    } else {
        // Find anything carrying the hierarchy columns you described
        $candidates = $conn->select(
            "SELECT TABLE_SCHEMA, TABLE_NAME, COUNT(DISTINCT COLUMN_NAME) as hits
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE COLUMN_NAME IN ('CUST_CODE','EMP_CODE','SalesMan','SuperVisor','salesManager')
             GROUP BY TABLE_SCHEMA, TABLE_NAME
             HAVING COUNT(DISTINCT COLUMN_NAME) >= 3
             ORDER BY COUNT(DISTINCT COLUMN_NAME) DESC"
        );
    }

    if (empty($candidates)) {
        echo "   !! nothing matched. Pass the view name: php explore_hierarchy.php MyViewName\n";
    }

    foreach ($candidates as $cand) {
        $name = $cand->TABLE_NAME;
        echo "\n--- [{$cand->TABLE_SCHEMA}].[{$name}]" . (isset($cand->hits) ? " ({$cand->hits} matching columns)" : '') . " ---\n";
        echo "  Columns:\n";
        printColumns($conn, $name);

        echo "  Sample rows:\n";
        printSample($conn, $name);

        try {
            $stats = $conn->selectOne(
                "SELECT COUNT(*) as total,
                        COUNT(DISTINCT CUST_CODE) as customers,
                        COUNT(DISTINCT EMP_CODE) as emp_codes,
                        COUNT(DISTINCT SalesMan) as salesmen,
                        COUNT(DISTINCT SuperVisor) as supervisors,
                        COUNT(DISTINCT salesManager) as managers
                 FROM [{$name}]"
            );
            echo "  Counts: rows={$stats->total} customers={$stats->customers} emp_codes={$stats->emp_codes} "
               . "salesmen={$stats->salesmen} supervisors={$stats->supervisors} managers={$stats->managers}\n";

            // Does one customer ever appear more than once?
            $dupes = $conn->selectOne(
                "SELECT COUNT(*) as c FROM (
                    SELECT CUST_CODE FROM [{$name}] GROUP BY CUST_CODE HAVING COUNT(*) > 1
                 ) x"
            );
            echo "  Customers appearing on more than one row: {$dupes->c}\n";

            // How well does it join to the aging report?
            $join = $conn->selectOne(
                "SELECT
                    (SELECT COUNT(*) FROM [Alarabia_AGING_SUMMARY]) as aging_rows,
                    (SELECT COUNT(*) FROM [Alarabia_AGING_SUMMARY] a
                     WHERE EXISTS (SELECT 1 FROM [{$name}] h WHERE h.CUST_CODE = a.[كود_العميل])) as matched"
            );
            echo "  Aging rows: {$join->aging_rows} | matched by CUST_CODE = كود_العميل: {$join->matched}\n";
        } catch (\Exception $e) {
            echo "  !! stats failed: " . $e->getMessage() . "\n";
        }
    }

    echo "\n" . str_repeat('=', 90) . "\n";
    echo "3) DOES THE AGING VIEW ALREADY CARRY THE HIERARCHY?\n";
    echo str_repeat('=', 90) . "\n";
    foreach (['SalesMan', 'SuperVisor', 'salesManager', 'EMP_CODE', 'Region_Parent'] as $needle) {
        $has = in_array($needle, $agingColumns, true) ? 'YES' : 'no';
        printf("   %-16s %s\n", $needle, $has);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
