<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel; // For CSV/Excel export
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::with('user');

        // Filter by action type
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by model name or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('model_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // Calculate stats for the current filtered set (before pagination)
        $statsQuery = $query->clone();
        $actionCounts = $statsQuery->reorder()
            ->select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();

        // Paginate
        $logs = $query->paginate(25);

        // Get filter options
        $actions = $this->getActionTypes();
        $modelTypes = $this->getModelTypes();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('admin.audit_logs.index', compact('logs', 'actions', 'modelTypes', 'users', 'actionCounts'));
    }

    /**
     * Show details of a specific audit log entry.
     */
    public function show(AuditLog $log)
    {
        $this->authorize('view', $log);
        $log->load('user');

        return view('admin.audit_logs.show', compact('log'));
    }

    /**
     * Export audit logs to CSV.
     */
    public function exportCSV(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::with('user');

        // Apply same filters as index
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->latest('created_at')->get();

        // Create CSV
        $csv = "Action,Model Type,Entity Name,User,Status,IP Address,Timestamp,Old Values,New Values\n";

        foreach ($logs as $log) {
            $oldValues = $log->formatted_old_values;
            $newValues = $log->formatted_new_values;
            $userName = $log->user ? $log->user->name : 'System';

            $csv .= "\"{$log->action_label}\",\"{$log->model_label}\",\"{$log->model_name}\",\"{$userName}\",\"{$log->status}\",\"{$log->ip_address}\",\"{$log->created_at->format('Y-m-d H:i:s')}\",\"{$oldValues}\",\"{$newValues}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="audit-logs-' . date('Y-m-d') . '.csv"');
    }

    /**
     * Export audit logs to Excel.
     */
    public function exportExcel(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::with('user');

        // Apply same filters as index
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->latest('created_at')->get();

        // Create exportable array
        $data = [];
        $data[] = ['Action', 'Model Type', 'Entity Name', 'User', 'Status', 'IP Address', 'Timestamp'];

        foreach ($logs as $log) {
            $userName = $log->user ? $log->user->name : 'System';
            $data[] = [
                $log->action_label,
                $log->model_label,
                $log->model_name,
                $userName,
                $log->status,
                $log->ip_address,
                $log->created_at->format('Y-m-d H:i:s'),
            ];
        }

        // Return Excel file (requires Maatwebsite/Laravel-Excel)
        return Excel::download(
            new \App\Exports\AuditLogsExport($data),
            'audit-logs-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Get summary statistics for dashboard.
     */
    public function getSummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $stats = AuditLog::getSummaryStats($startDate, $endDate);

        return response()->json($stats);
    }

    /**
     * Get audit logs for a specific entity.
     */
    public function getEntityLogs(Request $request)
    {
        $validated = $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $logs = AuditLog::forEntity($validated['model_type'], $validated['model_id'])
                        ->latest('created_at')
                        ->get();

        return response()->json($logs);
    }

    /**
     * Get recent logs for user dashboard.
     */
    public function getRecentLogs()
    {
        $logs = AuditLog::latest('created_at')
                        ->limit(10)
                        ->get();

        return response()->json($logs);
    }

    /**
     * Clear old audit logs (retention policy).
     */
    public function clearOldLogs(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:30|max:730',
            'confirm' => 'required|accepted',
        ]);

        try {
            $cutoffDate = now()->subDays($validated['days']);

            $deletedCount = AuditLog::where('created_at', '<', $cutoffDate)->delete();

            return back()->with('success', "Deleted {$deletedCount} audit logs older than {$validated['days']} days.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error clearing logs: ' . $e->getMessage());
        }
    }

    /**
     * Get action type options.
     */
    private function getActionTypes()
    {
        return [
            'CREATE' => 'Created',
            'UPDATE' => 'Updated',
            'DELETE' => 'Deleted',
            'DEACTIVATE' => 'Deactivated',
            'REACTIVATE' => 'Reactivated',
            'PASSWORD_RESET' => 'Password Reset',
            'ROLE_CHANGE' => 'Role Changed',
            'LOCK' => 'Account Locked',
            'UNLOCK' => 'Account Unlocked',
        ];
    }

    /**
     * Get model type options.
     */
    private function getModelTypes()
    {
        return [
            'Staff' => 'Staff Member',
            'TaskForce' => 'Task Force',
            'Configuration' => 'System Configuration',
            'Department' => 'Department',
            'User' => 'User Account',
        ];
    }
}
