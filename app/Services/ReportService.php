<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportService
{
    public function __construct(
        protected SalesHierarchyService $hierarchy
    ) {}

    /**
     * Cache key holding the version stamp of the per-user filter caches.
     */
    private const FILTERS_VERSION_KEY = 'aging_report_filters_version';

    /**
     * Columns the aging report table may be sorted by.
     */
    private const SORTABLE_COLUMNS = [
        'كود_العميل',
        'اسم_العميل',
        'تصنيف',
        'Region_Parent',
        'SalesMan',
        'اجمالي_مديونية_العميل',
        'Not Due',
        'Over Due',
    ];

    /**
     * Columns the region summary may be sorted by.
     */
    private const REGION_SORTABLE_COLUMNS = [
        'Region_Parent',
        'customers_count',
        'total_debt',
        'not_due',
        'overdue',
    ];

    /**
     * Columns the salesman summary may be sorted by.
     */
    private const SALESMAN_SORTABLE_COLUMNS = [
        'SalesMan',
        'Region_Parent',
        'customers_count',
        'total_debt',
        'not_due',
        'overdue',
    ];

    /**
     * Resolve a user supplied sort column against a whitelist.
     * Anything unknown falls back to the default column.
     */
    private function safeSortColumn(?string $column, array $allowed, string $default): string
    {
        return in_array($column, $allowed, true) ? $column : $default;
    }

    /**
     * Resolve a user supplied sort direction.
     */
    private function safeSortDirection(?string $direction): string
    {
        return strtolower((string) $direction) === 'asc' ? 'asc' : 'desc';
    }

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
     * Get base query with filters and security applied.
     */
    private function getBaseQuery(Report $report, array $filters = [])
    {
        $query = DB::connection('sqlsrv')->table($report->source_name)
            ->select('*', 'Region_Parent as Region_Display');

        // Security: restrict to the branch of the sales hierarchy this user owns.
        // The hierarchy itself lives in SQL Server, see SalesHierarchyService.
        $this->hierarchy->applyScope($query, auth()->user());

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
     * Bump the filter cache so every user rebuilds their options.
     * Call this whenever salesman assignments or user hierarchy change.
     */
    public static function flushFilterOptions(): void
    {
        Cache::forever(self::FILTERS_VERSION_KEY, self::filtersVersion() + 1);
    }

    private static function filtersVersion(): int
    {
        return (int) Cache::get(self::FILTERS_VERSION_KEY, 1);
    }

    /**
     * Filter dropdown options, restricted to the rows this user may see.
     * Cached per user so one user's list never leaks to another.
     */
    public function getFilterOptions(Report $report): array
    {
        $cacheKey = 'aging_report_filters_v' . self::filtersVersion() . '_user_' . (auth()->id() ?? 'guest');

        return Cache::remember($cacheKey, 3600, function () use ($report) {
            try {
                return [
                    'classifications' => $this->getBaseQuery($report)
                        ->whereNotNull('تصنيف')->distinct()->orderBy('تصنيف')->pluck('تصنيف'),
                    'regions' => $this->getBaseQuery($report)
                        ->whereNotNull('Region_Parent')->distinct()->orderBy('Region_Parent')->pluck('Region_Parent'),
                    'salesmen' => $this->getBaseQuery($report)
                        ->whereNotNull('SalesMan')->distinct()->orderBy('SalesMan')->pluck('SalesMan'),
                ];
            } catch (\Exception $e) {
                return ['classifications' => [], 'regions' => [], 'salesmen' => []];
            }
        });
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
        $sortColumn = $this->safeSortColumn($filters['sort_by'] ?? null, self::SORTABLE_COLUMNS, 'Over Due');
        $sortDirection = $this->safeSortDirection($filters['sort_dir'] ?? null);

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
        // Sort parameters for summaries
        $regionSort = $this->safeSortColumn($filters['region_sort_by'] ?? null, self::REGION_SORTABLE_COLUMNS, 'total_debt');
        $regionDir = $this->safeSortDirection($filters['region_sort_dir'] ?? null);
        $salesmanSort = $this->safeSortColumn($filters['salesman_sort_by'] ?? null, self::SALESMAN_SORTABLE_COLUMNS, 'total_debt');
        $salesmanDir = $this->safeSortDirection($filters['salesman_sort_dir'] ?? null);

        // Summary by Region
        $byRegion = $this->getBaseQuery($report, $filters)
            ->select('Region_Parent as Region_Display', DB::raw('COUNT(*) as customers_count'), DB::raw('SUM(اجمالي_مديونية_العميل) as total_debt'), DB::raw('SUM([Not Due]) as not_due'), DB::raw('SUM([Over Due]) as overdue'))
            ->whereNotNull('Region_Parent')
            ->groupBy('Region_Parent')
            ->orderBy($regionSort, $regionDir)
            ->get();

        // Map each salesman to the manager above him, straight from the
        // SQL Server hierarchy rather than from local user assignments.
        $salesmanToManagers = [];
        foreach ($this->hierarchy->chain() as $node) {
            if (!empty($node->SalesMan) && !empty($node->salesManager)) {
                $salesmanToManagers[$node->SalesMan][] = $node->salesManager;
            }
        }

        // Get all region-salesman pairs from SQL Server
        $regionSalesmen = $this->getBaseQuery($report, $filters)
            ->select('Region_Parent', 'SalesMan')
            ->whereNotNull('Region_Parent')
            ->whereNotNull('SalesMan')
            ->distinct()
            ->get()
            ->groupBy('Region_Parent');

        foreach ($byRegion as $region) {
            $salesmenInRegion = $regionSalesmen->get($region->Region_Display, collect())->pluck('SalesMan');
            $managers = [];
            foreach ($salesmenInRegion as $sm) {
                if (isset($salesmanToManagers[$sm])) {
                    $managers = array_merge($managers, $salesmanToManagers[$sm]);
                }
            }
            $region->area_manager = !empty($managers) ? implode(', ', array_unique($managers)) : 'غير محدد';
        }

        // Summary by Salesman
        $bySalesman = $this->getBaseQuery($report, $filters)
            ->select('SalesMan', 'Region_Parent as Region_Display', DB::raw('COUNT(*) as customers_count'), DB::raw('SUM(اجمالي_مديونية_العميل) as total_debt'), DB::raw('SUM([Not Due]) as not_due'), DB::raw('SUM([Over Due]) as overdue'))
            ->whereNotNull('SalesMan')
            ->groupBy('SalesMan', 'Region_Parent')
            ->orderBy($salesmanSort, $salesmanDir)
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
        $sortColumn = $this->safeSortColumn($filters['sort_by'] ?? null, self::SORTABLE_COLUMNS, 'Over Due');
        $sortDirection = $this->safeSortDirection($filters['sort_dir'] ?? null);
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
                'الكود', 'العميل', 'التصنيف', 'المنطقة', 'المندوب',
                'إجمالي المديونية', 'غير مستحق', 'Over Due', 'النسبة %',
                '1-7 يوم', '8-14 يوم', '15-22 يوم', '23-30 يوم',
                '31-60 يوم', '61-180 يوم', '+180 يوم'
            ]);

            $query->chunk(500, function($rows) use ($handle) {
                foreach ($rows as $row) {
                    $totalDebt = $row->{'اجمالي_مديونية_العميل'} ?? 0;
                    $overdue = $row->{'Over Due'} ?? 0;
                    $percent = $totalDebt > 0 ? ($overdue / $totalDebt) * 100 : 0;

                    fputcsv($handle, [
                        $row->{'كود_العميل'},
                        $row->{'اسم_العميل'},
                        $row->{'تصنيف'},
                        $row->{'Region_Display'},
                        $row->{'SalesMan'},
                        $totalDebt,
                        $row->{'Not Due'} ?? 0,
                        $overdue,
                        number_format($percent, 2) . ' %',
                        $row->{'1-7 Days'} ?? 0,
                        $row->{'8-14 Days'} ?? 0,
                        $row->{'15-22 Days'} ?? 0,
                        $row->{'23-30 Days'} ?? 0,
                        $row->{'31-60 Days'} ?? 0,
                        $row->{'61-180 Days'} ?? 0,
                        $row->{'+180 Days'} ?? 0,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
