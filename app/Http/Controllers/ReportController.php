<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Display a listing of reports.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('Admin')) {
            $reports = Report::all();
        } else {
            $roleIds = $user->roles->pluck('id');
            $reports = Report::whereHas('roles', function($q) use ($roleIds) {
                $q->whereIn('roles.id', $roleIds);
            })->get();
        }

        return view('reports.index', compact('reports'));
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report, Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasRole('Admin')) {
            $allowed = $report->roles()->whereIn('roles.id', $user->roles->pluck('id'))->exists();
            if (!$allowed) {
                abort(403);
            }
        }

        $page = $request->input('page', 1);

        // Custom view for aging report
        if ($report->code === 'aging_report') {
            $filters = $request->only(['search', 'classification', 'salesman', 'region', 'status', 'sort_by', 'sort_dir', 'region_sort_by', 'region_sort_dir', 'salesman_sort_by', 'salesman_sort_dir']);
            
            try {
                $data = $this->reportService->getAgingReportData($report, $filters, 15, $page);
                $statistics = $this->reportService->getAgingStatistics($report, $filters);
                
                // Get Top 10 Debtors (New)
                $topDebtors = $this->reportService->getTopDebtors($report, $filters);

                // Get Debt Summaries by Region and Salesman
                $debtSummaries = $this->reportService->getDebtSummaries($report, $filters);
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'SQLSTATE[08001]') || str_contains($e->getMessage(), 'timed out')) {
                    return view('errors.db_error', [
                        'report' => $report,
                        'error' => 'فشل الاتصال بخادم SQL Server. يرجى التأكد من تشغيل السيرفر أو تجربة وقت لاحق.'
                    ]);
                }
                throw $e;
            }

            // Get Filter Options (Cached for 60 minutes)
            $filterOptions = Cache::remember('aging_report_filters', 3600, function () use ($report) {
                try {
                    return [
                        'classifications' => DB::connection('sqlsrv')->table($report->source_name)
                            ->whereNotNull('تصنيف')->distinct()->orderBy('تصنيف')->pluck('تصنيف'),
                        'regions' => DB::connection('sqlsrv')->table($report->source_name)
                            ->whereNotNull('Region_Parent')->distinct()->orderBy('Region_Parent')->pluck('Region_Parent'),
                        'salesmen' => DB::connection('sqlsrv')->table($report->source_name)
                            ->whereNotNull('SalesMan')->distinct()->orderBy('SalesMan')->pluck('SalesMan'),
                    ];
                } catch (\Exception $e) {
                    return ['classifications' => [], 'regions' => [], 'salesmen' => []];
                }
            });

            return view('reports.aging_report', compact('report', 'data', 'statistics', 'filterOptions', 'topDebtors', 'debtSummaries'));
        }

        $data = $this->reportService->getReportData($report, 15, $page);

        return view('reports.show', compact('report', 'data'));
    }

    /**
     * Export report to Excel (CSV).
     */
    public function exportExcel(Report $report, Request $request)
    {
        $user = auth()->user();
        
        if (!$user->can('export excel') && !$user->hasRole('Admin')) {
            $allowed = $report->roles()->whereIn('roles.id', $user->roles->pluck('id'))->exists();
            if (!$allowed) {
                abort(403, 'غير مصرح لك بتصدير البيانات.');
            }
            
            // If they have report access but NOT export permission, abort
            if (!$user->can('export excel')) {
                abort(403, 'ليس لديك صلاحية تصدير ملفات اكسل.');
            }
        }

        if ($report->code === 'aging_report') {
            $filters = $request->only(['search', 'classification', 'salesman', 'region', 'status']);
            return $this->reportService->exportAgingReportToCsv($report, $filters);
        }

        abort(404, 'Export not supported for this report.');
    }
    public function top10(Report $report, Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasRole('Admin')) {
            $allowed = $report->roles()->whereIn('roles.id', $user->roles->pluck('id'))->exists();
            if (!$allowed) {
                abort(403);
            }
        }

        if ($report->code === 'aging_report') {
            $filters = $request->only(['salesman', 'region', 'status']);
            $limit = $request->input('limit', 10);
            
            // Validate limit (allow only specific values for safety or just ensure it's an int)
            $limit = is_numeric($limit) && $limit > 0 ? (int)$limit : 10;
            
            // Fetch Data
            $topDebtors = $this->reportService->getTopDebtors($report, $filters, $limit);
            $topSalesmen = $this->reportService->getTopSalesmen($report, $filters, $limit);

            // Get Filter Options (Cached)
            $filterOptions = Cache::remember('aging_report_filters', 3600, function () use ($report) {
                return [
                    'classifications' => DB::connection('sqlsrv')->table($report->source_name)
                        ->whereNotNull('تصنيف')->distinct()->orderBy('تصنيف')->pluck('تصنيف'),
                    'regions' => DB::connection('sqlsrv')->table($report->source_name)
                        ->whereNotNull('Region_Parent')->distinct()->orderBy('Region_Parent')->pluck('Region_Parent'),
                    'salesmen' => DB::connection('sqlsrv')->table($report->source_name)
                        ->whereNotNull('SalesMan')->distinct()->orderBy('SalesMan')->pluck('SalesMan'),
                ];
            });

            return view('reports.top_10', compact('report', 'topDebtors', 'topSalesmen', 'filterOptions', 'limit'));
        }

        abort(404);
    }
}
