<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ManagerSalesman;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SalesmanManagementController extends Controller
{
    /**
     * Show the management interface.
     */
    public function index()
    {
        try {
            $users = User::all();
            $managers = User::role(['Admin', 'Manager', 'General Manager', 'Area Manager', 'Supervisor'])->get();
            
            // Get all unique salesman names from SQL Server (cached)
            $salesmen = Cache::remember('sql_salesmen_list', 3600, function() {
                $report = Report::where('code', 'aging_report')->first();
                if (!$report) return [];
                
                return DB::connection('sqlsrv')->table($report->source_name)
                    ->whereNotNull('SalesMan')
                    ->distinct()
                    ->orderBy('SalesMan')
                    ->pluck('SalesMan');
            });

            $assignments = ManagerSalesman::with('manager')->paginate(10);

            return view('admin.salesmen_sync.index', compact('users', 'managers', 'salesmen', 'assignments'));
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'SQLSTATE[08001]') || str_contains($e->getMessage(), 'timed out')) {
                return view('errors.db_error', [
                    'error' => 'فشل الاتصال بخادم SQL Server لربط المناديب. يرجى التأكد من تشغيل السيرفر أو تجربة وقت لاحق.'
                ]);
            }
            throw $e;
        }
    }

    /**
     * Force sync salesman list from SQL Server.
     */
    public function sync()
    {
        Cache::forget('sql_salesmen_list');
        return back()->with('success', 'تم تحديث قائمة المندوبين بنجاح.');
    }

    /**
     * Assign a salesman name to a manager.
     */
    public function assign(Request $request)
    {
        $request->validate([
            'manager_id' => 'required|exists:users,id',
            'salesman_name' => 'required',
        ]);

        ManagerSalesman::updateOrCreate([
            'manager_id' => $request->manager_id,
            'salesman_name' => $request->salesman_name,
        ]);

        return back()->with('success', 'تم ربط المندوب بالمدير بنجاح.');
    }

    /**
     * Remove assignment.
     */
    public function unassign(User $manager, $salesman)
    {
        ManagerSalesman::where('manager_id', $manager->id)
            ->where('salesman_name', $salesman)
            ->delete();

        return back()->with('success', 'تم فك الارتباط بنجاح.');
    }
}
