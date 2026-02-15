<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/export-excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/{report}/top10', [App\Http\Controllers\ReportController::class, 'top10'])->name('reports.top10');

    // Settings
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    // Role-Report Permissions
    Route::get('/role-permissions', [App\Http\Controllers\RolePermissionController::class, 'index'])->name('role-permissions.index');
    Route::post('/role-permissions', [App\Http\Controllers\RolePermissionController::class, 'update'])->name('role-permissions.update');

    // User Management
    Route::resource('users', App\Http\Controllers\UserController::class);

    // Salesman Management
    Route::get('/salesmen-sync', [App\Http\Controllers\SalesmanManagementController::class, 'index'])->name('salesmen-sync.index');
    Route::post('/salesmen-sync/sync', [App\Http\Controllers\SalesmanManagementController::class, 'sync'])->name('salesmen-sync.sync');
    Route::post('/salesmen-sync/assign', [App\Http\Controllers\SalesmanManagementController::class, 'assign'])->name('salesmen-sync.assign');
    Route::delete('/salesmen-sync/unassign/{manager}/{salesman}', [App\Http\Controllers\SalesmanManagementController::class, 'unassign'])->name('salesmen-sync.unassign');
});


require __DIR__.'/auth.php';
