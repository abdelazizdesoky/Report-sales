<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportService
{
    /**
     * Fetch report data from the secondary SQL Server connection.
     *
     * @param Report $report
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getReportData(Report $report, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        // Use the 'sqlsrv' connection as defined in config/database.php
        $query = DB::connection('sqlsrv')->table($report->source_name);

        return $query->paginate(perPage: $perPage, page: $page);
    }

    /**
     * Fetch aging report data with filters.
     *
     * @param Report $report
     * @param array $filters
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    /**
     * Get base query with filters and security applied.
     */
    private function getBaseQuery(Report $report, array $filters = [])
    {
        $query = DB::connection('sqlsrv')->table($report->source_name)
            ->select('*', 'Region_Parent as Region_Display');

        // Security: Restrict based on user role and hierarchy
        $user = auth()->user();
        if ($user && !$user->hasRole('Admin') && !$user->hasRole('General Manager')) {
            $managedNames = $user->getManagedSalesmenNames();
            
            if (!empty($managedNames)) {
                // If the user is a manager or a salesman with a linked name, filter by the allowed set
                $query->whereIn('SalesMan', $managedNames);
            } else {
                // If for some reason they have no linked names, they should see nothing (unless they have no salesman_name and no managed salesmen)
                // However, if they have no linked names and are not admin, we might want to still check user->salesman_name
                // Actually getManagedSalesmenNames() returns salesman_name as well.
                // If it's empty, and they aren't admin, they see nothing by default or all if that's the intention?
                // Usually, if they have no role/salesman, they shouldn't see sensitive data.
                $query->where('SalesMan', 'NONE_MATCH');
            }
        }

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('NAME', 'like', "%{$search}%")
                  ->orWhere('كود_العميل', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['classification'])) {
            $query->where('تصنيف', $filters['classification']);
        }

        if (!empty($filters['salesman'])) {
            $query->where('SalesMan', $filters['salesman']);
        }

        if (!empty($filters['region'])) {
            $query->where('Region_Parent', $filters['region']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'overdue') {
                $query->where('Over Due', '>', 0);
            } elseif ($filters['status'] === 'due') {
                $query->where('Not Due', '>', 0);
            }
        }

        return $query;
    }

    /**
     * Fetch aging report data with filters.
     *
     * @param Report $report
     * @param array $filters
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getAgingReportData(Report $report, array $filters = [], int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $query = $this->getBaseQuery($report, $filters);

        // Default sorting
        $sortColumn = $filters['sort_by'] ?? 'Over Due';
        $sortDirection = $filters['sort_dir'] ?? 'desc';
        
        $query->orderBy($sortColumn, $sortDirection);

        return $query->paginate(perPage: $perPage, page: $page);
    }

    /**
     * Calculate statistics for aging report.
     */
    public function getAgingStatistics(Report $report, array $filters = []): array
    {
        $query = $this->getBaseQuery($report, $filters);

        return [
            'total_customers' => $query->count(),
            'total_debt' => $query->sum('اجمالي_مديونية_العميل'),
            'total_overdue' => $query->sum('Over Due'),
            'total_not_due' => $query->sum('Not Due'),
        ];
    }

    /**
     * Get Top Debtors.
     */
    public function getTopDebtors(Report $report, array $filters = [], int $limit = 10)
    {
        return $this->getBaseQuery($report, $filters)
            ->orderBy('اجمالي_مديونية_العميل', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get debt summaries by Region and Salesman.
     */
    public function getDebtSummaries(Report $report, array $filters = []): array
    {
        // Summary by Region
        $byRegion = $this->getBaseQuery($report, $filters)
            ->select('Region_Parent as Region_Display', DB::raw('COUNT(*) as customers_count'), DB::raw('SUM(اجمالي_مديونية_العميل) as total_debt'), DB::raw('SUM([Not Due]) as not_due'), DB::raw('SUM([Over Due]) as overdue'))
            ->whereNotNull('Region_Parent')
            ->groupBy('Region_Parent')
            ->orderBy('total_debt', 'desc')
            ->get();

        // Summary by Salesman
        $bySalesman = $this->getBaseQuery($report, $filters)
            ->select('SalesMan', 'Region_Parent as Region_Display', DB::raw('COUNT(*) as customers_count'), DB::raw('SUM(اجمالي_مديونية_العميل) as total_debt'), DB::raw('SUM([Not Due]) as not_due'), DB::raw('SUM([Over Due]) as overdue'))
            ->whereNotNull('SalesMan')
            ->groupBy('SalesMan', 'Region_Parent')
            ->orderBy('total_debt', 'desc')
            ->get();

        return [
            'by_region' => $byRegion,
            'by_salesman' => $bySalesman,
        ];
    }

    /**
     * Get Top 10 Salesmen by Total Debt.
     */
    public function getTopSalesmen(Report $report, array $filters = [], int $limit = 10)
    {
        // Clone the base query to avoid modifying the original if passed by reference (though here it returns new builder)
        $query = $this->getBaseQuery($report, $filters);

        return $query->select('SalesMan', DB::raw('SUM(اجمالي_مديونية_العميل) as total_debt'), DB::raw('COUNT(*) as customers_count'))
            ->whereNotNull('SalesMan')
            ->groupBy('SalesMan')
            ->orderBy('total_debt', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Export aging report to CSV.
     */
    public function exportAgingReportToCsv(Report $report, array $filters = [])
    {
        $query = $this->getBaseQuery($report, $filters);
        
        // Apply sorting for export too
        $sortColumn = $filters['sort_by'] ?? 'Over Due';
        $sortDirection = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortColumn, $sortDirection);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="aging_report_' . date('Y-m-d_H-i-s') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function() use ($query) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add Headers
            fputcsv($handle, [
                'كود العميل', 'اسم العميل', 'التصنيف', 'المنطقة', 'المندوب', 
                'إجمالي المديونية', 'غير مستحق', '1-30 يوم', '31-60 يوم', 
                '61-180 يوم', '+180 يوم', 'Over Due'
            ]);

            $query->chunk(500, function($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->{'كود_العميل'},
                        $row->{'اسم_العميل'},
                        $row->{'تصنيف'},
                        $row->{'Region_Display'},
                        $row->{'SalesMan'},
                        $row->{'اجمالي_مديونية_العميل'},
                        $row->{'Not Due'},
                        $row->{'1-7 Days'} + $row->{'8-14 Days'} + $row->{'15-22 Days'} + $row->{'23-30 Days'},
                        $row->{'31-60 Days'},
                        $row->{'61-180 Days'},
                        $row->{'+180 Days'},
                        $row->{'Over Due'},
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Execute a stored procedure if source_name is likely an SP.
     * 
     * @param string $spName
     * @param array $params
     * @return array
     */
    public function executeStoredProcedure(string $spName, array $params = []): array
    {
        // Example: DB::connection('sqlsrv')->select('EXEC SP_Name ?, ?', [$val1, $val2]);
        // This is a placeholder for SP logic if needed.
        return DB::connection('sqlsrv')->select("EXEC $spName", $params);
    }
}
