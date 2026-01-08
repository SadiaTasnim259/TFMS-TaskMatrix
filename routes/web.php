<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Admin\TaskForceController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkloadController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\PerformanceController;



// FIX DB ROUTE - EMERGENCY


/**
 * GUEST ROUTES
 * 
 * Users NOT logged in can access these
 * If they try to access a /login route while logged in,
 * they'll be redirected to dashboard
 */
Route::middleware('guest')->group(function () {

    // LOGIN ROUTES
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');



    // FORGOT PASSWORD ROUTES
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->name('password.email');

    // PASSWORD RESET ROUTES
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');
});

/**
 * AUTHENTICATED ROUTES
 * 
 * Only logged-in users can access these
 * If not logged in, redirected to /login
 */
Route::middleware(['auth', 'prevent-back-history'])->group(function () {

    // LOGOUT ROUTE
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // CHANGE PASSWORD ROUTE (first login)
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])
        ->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->name('change-password.post');

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

/**
 * HOME ROUTE
 * 
 * When user visits /, redirect to login
 */
Route::get('/', function () {
    return redirect('/login');
});

/**
 * ADMIN ROUTES (MODULE 2)
 *
 * Only authenticated admins can access these routes.
 */
Route::middleware(['auth', 'admin', 'prevent-back-history'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // System Report
        Route::get('system-report', [DashboardController::class, 'downloadSystemReport'])->name('system-report');



        // Departments
        Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);

        // Task forces
        Route::resource('task-forces', TaskForceController::class);
        Route::post('task-forces/{task_force}/toggle-status', [TaskForceController::class, 'toggleStatus'])
            ->name('task-forces.toggle-status');
        Route::get('task-forces/{task_force}/assign-departments', [TaskForceController::class, 'assignDepartmentsForm'])
            ->name('task-forces.assign-departments.form');
        Route::post('task-forces/{task_force}/assign-departments', [TaskForceController::class, 'assignDepartments'])
            ->name('task-forces.assign-departments');

        // Workload Thresholds
        Route::get('workload-thresholds', [App\Http\Controllers\Admin\WorkloadThresholdController::class, 'edit'])
            ->name('workload.thresholds.edit');
        Route::post('workload-thresholds', [App\Http\Controllers\Admin\WorkloadThresholdController::class, 'update'])
            ->name('workload.thresholds.update');

        // Academic Sessions (New Module)
        Route::patch('/academic-sessions/{academic_session}/activate', [App\Http\Controllers\Admin\AcademicSessionController::class, 'activate'])->name('academic-sessions.activate');
        Route::resource('academic-sessions', App\Http\Controllers\Admin\AcademicSessionController::class);

        // Users
        Route::get('users/import', [UserController::class, 'import'])->name('users.import');
        Route::post('users/import', [UserController::class, 'upload'])->name('users.upload');
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
        Route::post('users/{user}/change-role', [UserController::class, 'changeRole'])
            ->name('users.change-role');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
        Route::post('users/{user}/unlock', [UserController::class, 'unlockAccount'])
            ->name('users.unlock');

        // Audit logs
        Route::get('audit-logs', [AuditLogController::class, 'index'])
            ->name('audit-logs.index');
        Route::get('audit-logs/{log}', [AuditLogController::class, 'show'])
            ->name('audit-logs.show');
        Route::get('audit-logs/export/csv', [AuditLogController::class, 'exportCsv'])
            ->name('audit-logs.export-csv');
        Route::get('audit-logs/export/excel', [AuditLogController::class, 'exportExcel'])
            ->name('audit-logs.export-excel');
    });

/**
 * HOD ROUTES
 */
Route::middleware(['auth', 'role:hod', 'prevent-back-history'])
    ->prefix('hod')
    ->name('hod.')
    ->group(function () {
        Route::get('task-forces', [App\Http\Controllers\HOD\TaskForceController::class, 'index'])->name('task-forces.index');
        Route::get('task-forces/{task_force}', [App\Http\Controllers\HOD\TaskForceController::class, 'show'])->name('task-forces.show');
        Route::post('task-forces/{task_force}/members', [App\Http\Controllers\HOD\TaskForceController::class, 'addMember'])->name('task-forces.add-member');
        Route::delete('task-forces/{task_force}/members/{staff}', [App\Http\Controllers\HOD\TaskForceController::class, 'removeMember'])->name('task-forces.remove-member');
        Route::post('task-forces/{task_force}/submit', [App\Http\Controllers\HOD\TaskForceController::class, 'submitRequests'])->name('task-forces.submit-requests');
        Route::delete('task-forces/{task_force}/drafts/{membership_request}', [App\Http\Controllers\HOD\TaskForceController::class, 'deleteDraft'])->name('task-forces.delete-draft');
        Route::delete('task-forces/{task_force}/requests/{membership_request}', [App\Http\Controllers\HOD\TaskForceController::class, 'cancelRequest'])->name('task-forces.cancel-request');
        Route::patch('task-forces/{task_force}/requests/{membership_request}', [App\Http\Controllers\HOD\TaskForceController::class, 'updateRequest'])->name('task-forces.update-request');

        // Workload
        Route::get('workload', [App\Http\Controllers\HOD\WorkloadController::class, 'index'])->name('workload.index');

        Route::get('workload/{staff}', [App\Http\Controllers\HOD\WorkloadController::class, 'show'])->name('workload.show');
    });

/**
 * MODULE 3: WORKLOAD, REPORTS, ANALYTICS & PERFORMANCE
 *
 * Authenticated users with appropriate roles can access these routes.
 */
Route::middleware(['auth', 'prevent-back-history'])
    ->group(function () {

        Route::prefix('workload')->name('workload.')->group(function () {
            Route::get('/remarks', [WorkloadController::class, 'remarksForm'])->name('remarks');
            Route::post('/remarks', [WorkloadController::class, 'submitRemarks'])->name('remarks.submit');
            Route::get('/history', [WorkloadController::class, 'history'])->name('history');
            Route::get('/summary/download', [WorkloadController::class, 'downloadSummaryPdf'])->name('summary.download');
            Route::get('/summary', [WorkloadController::class, 'summary'])->name('summary');
            Route::get('/', [WorkloadController::class, 'index'])->name('index');
            Route::get('/assigned-task-forces', [WorkloadController::class, 'assignedTaskForces'])->name('assigned-task-forces');
            Route::get('/create', [WorkloadController::class, 'create'])->name('create');
            Route::post('/', [WorkloadController::class, 'store'])->name('store');
            Route::get('/{submission}', [WorkloadController::class, 'show'])->name('show');
            Route::get('/{submission}/edit', [WorkloadController::class, 'edit'])->name('edit');
            Route::post('/{submission}/add-activity', [WorkloadController::class, 'addActivity'])->name('add-activity');
            Route::delete('/{submission}/activity/{item}', [WorkloadController::class, 'removeActivity'])->name('remove-activity');
            Route::post('/{submission}/submit', [WorkloadController::class, 'submit'])->name('submit');
            Route::post('/{submission}/approve', [WorkloadController::class, 'approve'])->name('approve');
            Route::post('/{submission}/reject', [WorkloadController::class, 'reject'])->name('reject');
        });

        // Reports (HOD, Management, Admin)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/create', [ReportController::class, 'create'])->name('create');
            Route::post('/staff-workload', [ReportController::class, 'generateStaffWorkload'])->name('staff-workload');
            Route::post('/department-workload', [ReportController::class, 'generateDepartmentWorkload'])->name('department-workload');
            Route::post('/performance-report', [ReportController::class, 'generatePerformanceReport'])->name('performance-report');
            Route::post('/task-force-report', [ReportController::class, 'generateTaskForceReport'])->name('task-force-report');
            Route::get('/{report}/download', [ReportController::class, 'download'])->name('download');

            // Export routes (R3.4.5, R3.4.6, R3.8.1, R3.8.2, R3.8.3)
            Route::post('/export/workload-excel', [ReportController::class, 'exportWorkloadExcel'])->name('export.workload-excel');
            Route::post('/export/workload-csv', [ReportController::class, 'exportWorkloadCsv'])->name('export.workload-csv');
            Route::post('/export/performance-excel', [ReportController::class, 'exportPerformanceExcel'])->name('export.performance-excel');

            // Show report (must be last to avoid conflict with specific routes)
            Route::get('/{report}', [ReportController::class, 'show'])->name('show');
        });

        // Analytics Dashboard (Admin only)
        Route::prefix('analytics')->name('analytics.')->middleware('admin')->group(function () {
            Route::get('/', [AnalyticsController::class, 'index'])->name('index');
            Route::post('/snapshot', [AnalyticsController::class, 'generateSnapshot'])->name('snapshot');
            Route::get('/snapshots', [AnalyticsController::class, 'snapshots'])->name('snapshots');
        });

        // Performance Evaluation (HOD, Admin)
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/', [PerformanceController::class, 'index'])->name('index');
            Route::get('/staff/{staff}/edit', [PerformanceController::class, 'edit'])->name('edit');
            Route::post('/staff/{staff}', [PerformanceController::class, 'update'])->name('update');
            Route::get('/{score}', [PerformanceController::class, 'show'])->name('show');
        });
    });

/**
 * PSM ROUTES (Module 3 - Part 2)
 */
Route::middleware(['auth', 'role:psm', 'prevent-back-history'])
    ->prefix('psm')
    ->name('psm.')
    ->group(function () {
        // Workload Management
        Route::get('workload', [App\Http\Controllers\PSM\WorkloadController::class, 'index'])->name('workload.index');
        Route::get('workload/imbalance', [App\Http\Controllers\PSM\WorkloadController::class, 'imbalanceReport'])->name('workload.imbalance');
        Route::get('workload/{department}', [App\Http\Controllers\PSM\WorkloadController::class, 'show'])->name('workload.show');
        Route::post('workload/{department}/approve', [App\Http\Controllers\PSM\WorkloadController::class, 'approve'])->name('workload.approve');
        Route::post('workload/{department}/reject', [App\Http\Controllers\PSM\WorkloadController::class, 'reject'])->name('workload.reject');

        // Reports
        Route::get('reports', [App\Http\Controllers\PSM\ReportController::class, 'index'])->name('reports.index');
        Route::post('reports/generate', [App\Http\Controllers\PSM\ReportController::class, 'generate'])->name('reports.generate');

        // Membership Requests
        Route::get('task-forces/requests', [App\Http\Controllers\PSM\TaskForceController::class, 'indexRequests'])->name('task-forces.requests');
        Route::post('task-forces/{task_force}/requests/{membership_request}/approve', [App\Http\Controllers\PSM\TaskForceController::class, 'approveRequest'])->name('task-forces.approve-request');
        Route::post('task-forces/{task_force}/requests/{membership_request}/reject', [App\Http\Controllers\PSM\TaskForceController::class, 'rejectRequest'])->name('task-forces.reject-request');

        // Exceptional Modification (Unlock)
        Route::post('task-forces/{task_force}/unlock', [App\Http\Controllers\PSM\TaskForceController::class, 'unlockTaskForce'])->name('task-forces.unlock');

        // Faculty Task Force Directory
        Route::get('task-forces', [App\Http\Controllers\PSM\TaskForceController::class, 'index'])->name('task-forces.index');
        Route::get('task-forces/{task_force}', [App\Http\Controllers\PSM\TaskForceController::class, 'show'])->name('task-forces.show');
        Route::post('task-forces/{task_force}/members', [App\Http\Controllers\PSM\TaskForceController::class, 'addMember'])->name('task-forces.add-member');
        Route::delete('task-forces/{task_force}/members/{user}', [App\Http\Controllers\PSM\TaskForceController::class, 'removeMember'])->name('task-forces.remove-member');
        Route::post('task-forces/{task_force}/lock', [App\Http\Controllers\PSM\TaskForceController::class, 'lockTaskForce'])->name('task-forces.lock');
    });


/**
 * MANAGEMENT ROUTES (MODULE 3)
 *
 * Only authenticated management users can access these routes.
 */
Route::middleware(['auth', 'role:management', 'prevent-back-history'])
    ->prefix('management')
    ->name('management.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Management\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/task-distribution', [App\Http\Controllers\Management\DashboardController::class, 'taskDistribution'])->name('task_distribution');
        Route::get('/department-comparison', [App\Http\Controllers\Management\DashboardController::class, 'departmentComparison'])->name('department_comparison');
        Route::get('/export-reports', [App\Http\Controllers\Management\DashboardController::class, 'exportReports'])->name('export_reports');
        Route::post('/export-reports', [App\Http\Controllers\Management\DashboardController::class, 'generateReport'])->name('generate_report');
        Route::get('/dashboard/export', [App\Http\Controllers\Management\DashboardController::class, 'export'])->name('export');
        Route::get('/department/{department}', [App\Http\Controllers\Management\DashboardController::class, 'department'])->name('department');
    });
