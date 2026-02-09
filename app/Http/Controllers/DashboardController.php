<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'users_count' => User::count(),
            'reports_count' => Report::count(),
            'last_report_date' => Report::latest()->first()?->created_at->format('Y-m-d') ?? 'N/A',
        ];

        // Get reports the user has permission to view
        $reports = Report::whereHas('roles', function($q) use ($user) {
            $q->whereIn('roles.id', $user->roles->pluck('id'));
        })->get();

        return view('dashboard', compact('stats', 'reports'));
    }
}
