<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TaskForceController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DepartmentController;

/**
 * Admin Routes (requires auth + admin role)
 * Prefix: /admin
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // ========== DASHBOARD ==========
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // ========== STAFF MANAGEMENT (Redirect or handled by User Management) ==========
    // Since we merged, we can redirect or just remove.
    // Ensure UserController handles imports/exports which were on StaffController

    // ========== TASK FORCE MANAGEMENT ==========
    Route::resource('task-forces', TaskForceController::class, ['as' => 'admin']);

    // Custom task force routes
    Route::prefix('task-forces')->group(function () {
        Route::post('{taskForce}/toggle-status', [TaskForceController::class, 'toggleStatus'])->name('admin.task-forces.toggle-status');
        Route::get('{taskForce}/assign-departments', [TaskForceController::class, 'assignDepartmentsForm'])->name('admin.task-forces.assign-departments-form');
        Route::post('{taskForce}/assign-departments', [TaskForceController::class, 'assignDepartments'])->name('admin.task-forces.assign-departments');
        Route::delete('{taskForce}/departments/{department}', [TaskForceController::class, 'removeDepartment'])->name('admin.task-forces.remove-department');
        Route::get('export', [TaskForceController::class, 'export'])->name('admin.task-forces.export');
    });

    // ========== DEPARTMENT MANAGEMENT ==========
    Route::resource('departments', DepartmentController::class, ['as' => 'admin']);

    // Custom department routes
    Route::prefix('departments')->group(function () {
        Route::post('{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('admin.departments.toggle-status');
        Route::post('{department}/assign-head', [DepartmentController::class, 'assignHead'])->name('admin.departments.assign-head');
        Route::delete('{department}/head', [DepartmentController::class, 'removeHead'])->name('admin.departments.remove-head');
    });

    // ========== USER MANAGEMENT ==========
    Route::resource('users', UserController::class, ['as' => 'admin']);

    // Custom user routes
    Route::prefix('users')->group(function () {
        Route::get('{user}/reset-password-form', [UserController::class, 'showResetPasswordForm'])->name('admin.users.reset-password-form');
        Route::post('{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
        Route::post('{user}/change-role', [UserController::class, 'changeRole'])->name('admin.users.change-role');
        Route::post('{user}/unlock', [UserController::class, 'unlockAccount'])->name('admin.users.unlock');
        Route::post('{user}/activate', [UserController::class, 'activate'])->name('admin.users.activate');
        Route::post('{user}/deactivate', [UserController::class, 'deactivate'])->name('admin.users.deactivate');
        Route::post('{user}/force-password-change', [UserController::class, 'forcePasswordChange'])->name('admin.users.force-password-change');
        Route::get('export', [UserController::class, 'export'])->name('admin.users.export');
    });

    // ========== CONFIGURATION MANAGEMENT ==========
    Route::get('configuration', [ConfigurationController::class, 'index'])->name('admin.configuration.index');

    Route::prefix('configuration')->group(function () {
        // Academic session
        Route::get('academic-session/edit', [ConfigurationController::class, 'editAcademicSession'])->name('admin.configuration.academic-session-edit');
        Route::post('academic-session/update', [ConfigurationController::class, 'updateAcademicSession'])->name('admin.configuration.academic-session-update');

        // Thresholds
        Route::get('thresholds/edit', [ConfigurationController::class, 'editThresholds'])->name('admin.configuration.thresholds-edit');
        Route::post('thresholds/update', [ConfigurationController::class, 'updateThresholds'])->name('admin.configuration.thresholds-update');

        // Weightages
        Route::get('weightages/edit', [ConfigurationController::class, 'editWeightages'])->name('admin.configuration.weightages-edit');
        Route::post('weightages/update', [ConfigurationController::class, 'updateWeightages'])->name('admin.configuration.weightages-update');

        // Performance Weights
        Route::get('performance-weights/edit', [ConfigurationController::class, 'editPerformanceWeights'])->name('admin.configuration.performance-weights-edit');
        Route::post('performance-weights/update', [ConfigurationController::class, 'updatePerformanceWeights'])->name('admin.configuration.performance-weights-update');

        // Reset
        Route::get('reset', [ConfigurationController::class, 'resetDefaults'])->name('admin.configuration.reset');
        Route::post('reset/confirm', [ConfigurationController::class, 'confirmReset'])->name('admin.configuration.reset-confirm');
    });

    // ========== AUDIT LOG VIEWING ==========
    Route::resource('audit-logs', AuditLogController::class, [
        'only' => ['index', 'show'],
        'as' => 'admin'
    ]);

    // Custom audit log routes
    Route::prefix('audit-logs')->group(function () {
        Route::get('export-csv', [AuditLogController::class, 'exportCSV'])->name('admin.audit-logs.export-csv');
        Route::get('export-excel', [AuditLogController::class, 'exportExcel'])->name('admin.audit-logs.export-excel');
        Route::get('summary', [AuditLogController::class, 'getSummary'])->name('admin.audit-logs.summary');
        Route::get('entity-logs', [AuditLogController::class, 'getEntityLogs'])->name('admin.audit-logs.entity-logs');
        Route::get('recent', [AuditLogController::class, 'getRecentLogs'])->name('admin.audit-logs.recent');
        Route::post('clear-old', [AuditLogController::class, 'clearOldLogs'])->name('admin.audit-logs.clear-old');
    });

});

/**
 * API Routes for AJAX/Frontend (optional)
 * Prefix: /api/admin
 */
Route::middleware(['auth', 'admin'])->prefix('api/admin')->group(function () {

    // Get active staff (users with staff details) for dropdowns
    Route::get('staff/active', function () {
        $staff = \App\Models\User::active()
            ->with('department')
            ->orderBy('first_name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->full_name ?? $s->name,
                    'email' => $s->email,
                    'department' => $s->department->name ?? 'N/A',
                ];
            });

        return response()->json($staff);
    })->name('admin.api.staff.active');

    // Get active departments for dropdowns
    Route::get('departments/active', function () {
        $departments = \App\Models\Department::active()
            ->orderBy('name')
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'name' => $d->name,
                    'code' => $d->code,
                ];
            });

        return response()->json($departments);
    })->name('admin.api.departments.active');

    // Get active task forces for dropdowns
    Route::get('task-forces/active', function () {
        $taskForces = \App\Models\TaskForce::active()
            ->orderBy('name')
            ->get()
            ->map(function ($tf) {
                return [
                    'id' => $tf->id,
                    'name' => $tf->name,
                    'category' => $tf->category,
                ];
            });

        return response()->json($taskForces);
    })->name('admin.api.task-forces.active');

    // Get audit log summary
    Route::get('audit-logs/summary', [AuditLogController::class, 'getSummary'])->name('admin.api.audit-logs.summary');

});
