<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Ensure the current user's roles grant access to this report.
     */
    private function authorizeReport(Report $report, string $message = 'غير مصرح لك بعرض هذا التقرير.'): void
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            return;
        }

        $allowed = $report->roles()->whereIn('roles.id', $user->roles->pluck('id'))->exists();

        if (!$allowed) {
            abort(403, $message);
        }
    }

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
        $this->authorizeReport($report);

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

                // Filter options, scoped to what this user is allowed to see
                $filterOptions = $this->reportService->getFilterOptions($report);
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'SQLSTATE[08001]') || str_contains($e->getMessage(), 'timed out')) {
                    return view('errors.db_error', [
                        'report' => $report,
                        'error' => 'فشل الاتصال بخادم SQL Server. يرجى التأكد من تشغيل السيرفر أو تجربة وقت لاحق.'
                    ]);
                }
                throw $e;
            }

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

        if (!$user->can('export excel')) {
            abort(403, 'ليس لديك صلاحية تصدير ملفات اكسل.');
        }

        $this->authorizeReport($report, 'غير مصرح لك بتصدير هذا التقرير.');

        if ($report->code === 'aging_report') {
            $filters = $request->only(['search', 'classification', 'salesman', 'region', 'status', 'sort_by', 'sort_dir']);
            return $this->reportService->exportAgingReportToCsv($report, $filters);
        }

        abort(404, 'Export not supported for this report.');
    }

    public function top10(Report $report, Request $request)
    {
        $this->authorizeReport($report);

        if ($report->code === 'aging_report') {
            $filters = $request->only(['salesman', 'region', 'status']);
            $limit = $request->input('limit', 10);

            // Validate limit (allow only specific values for safety or just ensure it's an int)
            $limit = is_numeric($limit) && $limit > 0 ? min((int)$limit, 100) : 10;

            // Fetch Data
            $topDebtors = $this->reportService->getTopDebtors($report, $filters, $limit);
            $topSalesmen = $this->reportService->getTopSalesmen($report, $filters, $limit);

            // Filter options, scoped to what this user is allowed to see
            $filterOptions = $this->reportService->getFilterOptions($report);

            return view('reports.top_10', compact('report', 'topDebtors', 'topSalesmen', 'filterOptions', 'limit'));
        }

        abort(404);
    }
}
