<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\WorkloadSubmission;
use App\Models\PerformanceScore;
use App\Models\User;
use App\Models\Department;
use App\Models\TaskForce;
use App\Models\AuditLog;
use App\Exports\WorkloadSubmissionsExport;
use App\Exports\PerformanceScoresExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Show reports listing
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Report::class);
        $user = Auth::user();

        $query = Report::with('generatedBy', 'staff', 'department', 'taskForce');

        // Filter by report type
        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->byYear($request->year);
        }

        // Show latest 50 reports, 10 per page
        $latestReports = $query->recent()->take(50)->get();
        $page = $request->get('page', 1);
        $perPage = 10;

        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $latestReports->forPage($page, $perPage)->values(),
            $latestReports->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Statistics for dashboard
        $totalReports = Report::count();
        $monthlyReports = Report::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $departmentCount = Department::count();
        $staffCount = User::active()->whereNotNull('department_id')->count();

        // Filter Lists
        $departments = Department::orderBy('name')->get();
        $academicYears = ['2024/2025', '2025/2026']; // Hardcoded for now as per previous version

        $reportTypes = [
            'staff_workload' => 'Staff Workload',
            'department_workload' => 'Department Workload',
            'performance_evaluation' => 'Performance Evaluation',
            'task_force_performance' => 'Task Force Performance',
            'analytics_snapshot' => 'Analytics Snapshot',
            'institutional_summary' => 'Institutional Summary',
        ];

        return view('reports.index', compact(
            'reports',
            'reportTypes',
            'academicYears',
            'departments',
            'totalReports',
            'monthlyReports',
            'departmentCount',
            'staffCount'
        ));
    }

    /**
     * Show specific report
     */
    public function show(Report $report)
    {
        $this->authorize('view', $report);

        // Re-construct request to generate view calls
        $request = new Request();
        // Manually set parameters from the saved report
        $request->merge([
            'academic_year' => $report->academic_year,
            'semester' => $report->semester,
            // Pass format if one was originally requested or default to html
            'format' => request()->input('format', $report->file_format ?? 'html'),
        ]);

        if ($report->report_type === 'staff_workload') {
            $request->merge(['staff_id' => $report->user_id]);
            return $this->generateStaffWorkload($request);
        }

        if ($report->report_type === 'department_workload') {
            $request->merge(['department_id' => $report->department_id]);
            return $this->generateDepartmentWorkload($request);
        }

        if ($report->report_type === 'performance_evaluation') {
            if ($report->user_id)
                $request->merge(['staff_id' => $report->user_id]);
            if ($report->department_id)
                $request->merge(['department_id' => $report->department_id]);
            return $this->generatePerformanceReport($request);
        }

        if ($report->report_type === 'task_force_performance') {
            $request->merge(['task_force_id' => $report->task_force_id]);
            return $this->generateTaskForceReport($request);
        }

        return redirect()->back()->with('error', 'Unknown report type');
    }

    /**
     * Generate staff workload report
     */
    public function generateStaffWorkload(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:1,2,annual',
        ]);

        $staff = User::findOrFail($validated['staff_id']);
        $this->authorize('view', $staff);

        // Get workload submissions
        $submissions = WorkloadSubmission::where('user_id', $staff->id)
            ->where('academic_year', $validated['academic_year'])
            ->when($validated['semester'] !== 'annual', function ($q) use ($validated) {
                $q->where('semester', $validated['semester']);
            })
            ->with('items')
            ->get();

        // Create report record
        $report = Report::create([
            'report_type' => 'staff_workload',
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'user_id' => $staff->id,
            'file_format' => 'html',
            'generated_by' => auth()->id(),
            'generated_at' => now(),
            'total_records' => $submissions->count(),
            'summary' => "Workload report for {$staff->fullName()}",
        ]);

        // Log report generation (R3.10.3)
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'report_generated',
            'model_type' => Report::class,
            'model_id' => $report->id,
            'new_values' => json_encode(['report_type' => 'staff_workload', 'staff' => $staff->fullName()]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Check if PDF export is requested
        if ($request->has('format') && $request->input('format') === 'pdf') {
            $pdf = Pdf::loadView('reports.staff-workload-pdf', compact('report', 'staff', 'submissions'));
            $fileName = "staff-workload-{$staff->staff_id}-{$validated['academic_year']}.pdf";

            return $pdf->download($fileName);
        }

        // Calculate Summary Stats
        $summary = [
            'total_submissions' => $submissions->count(),
            'total_hours' => $submissions->sum('total_hours'),
            'total_credits' => $submissions->sum('total_credits'),
            'total_activities' => $submissions->sum(function ($s) {
                return $s->items->count();
            }),
        ];

        return view('reports.staff-workload', compact('report', 'staff', 'submissions', 'summary'));
    }

    /**
     * Generate department workload report
     */
    public function generateDepartmentWorkload(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:1,2,annual',
        ]);

        $department = Department::with('staff')->findOrFail($validated['department_id']);
        $this->authorize('view', $department);
        $totalStaff = $department->staff()->count();

        // Get all submissions for department staff
        $submissions = WorkloadSubmission::byDepartment($department->id)
            ->where('academic_year', $validated['academic_year'])
            ->when($validated['semester'] !== 'annual', function ($q) use ($validated) {
                $q->where('semester', $validated['semester']);
            })
            ->with('user', 'items')
            ->get();

        // Create report record
        $report = Report::create([
            'report_type' => 'department_workload',
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'department_id' => $department->id,
            'file_format' => 'html',
            'generated_by' => auth()->id(),
            'generated_at' => now(),
            'total_records' => $submissions->count(),
            'summary' => "Department workload report for {$department->name}",
        ]);

        // Calculate Activity Breakdown
        $activityBreakdown = [];
        $totalItems = 0;

        foreach ($submissions as $submission) {
            foreach ($submission->items as $item) {
                $type = $item->activity_type;
                if (!isset($activityBreakdown[$type])) {
                    $activityBreakdown[$type] = [
                        'count' => 0,
                        'hours' => 0,
                        'credits' => 0,
                        'color' => $item->getActivityTypeColor(),
                    ];
                }
                $activityBreakdown[$type]['count']++;
                $activityBreakdown[$type]['hours'] += $item->hours_allocated;
                $activityBreakdown[$type]['credits'] += $item->credits_value;
                $totalItems++;
            }
        }

        // Calculate percentages
        foreach ($activityBreakdown as $type => &$data) {
            $data['percentage'] = $totalItems > 0 ? ($data['count'] / $totalItems) * 100 : 0;
        }
        unset($data);

        // Calculate Staff Workloads
        $staffWorkloads = $submissions->groupBy('user_id')->map(function ($staffSubmissions) {
            $staff = $staffSubmissions->first()->user;
            return [
                'staff_name' => $staff ? $staff->name : 'Unknown',
                'staff_id' => $staff ? ($staff->employee_id ?? $staff->id) : 'N/A',
                'submissions_count' => $staffSubmissions->count(),
                'total_hours' => $staffSubmissions->sum('total_hours'),
                'total_credits' => $staffSubmissions->sum('total_credits'),
                'activities_count' => $staffSubmissions->sum(function ($s) {
                    return $s->items->count();
                }),
                'has_pending' => $staffSubmissions->contains('status', 'submitted'),
                'all_approved' => $staffSubmissions->every('status', 'approved'),
            ];
        })->values();

        // Calculate Summary Stats
        $summary = [
            'total_submissions' => $submissions->count(),
            'total_hours' => $submissions->sum('total_hours'),
            'total_credits' => $submissions->sum('total_credits'),
            'avg_hours_per_staff' => $staffWorkloads->count() > 0 ? $submissions->sum('total_hours') / $staffWorkloads->count() : 0,
        ];

        return view('reports.department-workload', compact('report', 'department', 'submissions', 'activityBreakdown', 'staffWorkloads', 'summary', 'totalStaff'));
    }

    /**
     * Generate performance evaluation report (R3.4.3)
     */
    public function generatePerformanceReport(Request $request)
    {
        $this->authorize('viewAny', PerformanceScore::class);

        $validated = $request->validate([
            'staff_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:1,2,annual',
            'rating' => 'nullable|in:excellent,good,satisfactory,needs_improvement',
        ]);

        $query = PerformanceScore::with('staff.department', 'evaluatedBy')
            ->byYear($validated['academic_year']);

        // Enforce HOD restriction
        if (Auth::user()->isHOD()) {
            $deptId = Auth::user()->department_id;
            // If they requested a different department, fail or override
            if (!empty($validated['department_id']) && $validated['department_id'] != $deptId) {
                abort(403, 'Unauthorized to view other departments');
            }
            // Force department filter
            $query->whereHas('staff', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        // Filter by semester if not annual
        if ($validated['semester'] !== 'annual') {
            $query->bySemester($validated['semester']);
        }

        // Filter by specific staff
        if (!empty($validated['staff_id'])) {
            $query->byStaff($validated['staff_id']);
        }

        // Filter by department
        if (!empty($validated['department_id'])) {
            $query->whereHas('staff', function ($q) use ($validated) {
                $q->where('department_id', $validated['department_id']);
            });
        }

        // Filter by rating
        if (!empty($validated['rating'])) {
            $query->byRating($validated['rating']);
        }

        $scores = $query->get();

        // Calculate statistics
        $stats = [
            'total_count' => $scores->count(),
            'average_score' => $scores->avg('overall_score'),
            'highest_score' => $scores->max('overall_score'),
            'lowest_score' => $scores->min('overall_score'),
            'excellent_count' => $scores->where('rating', 'excellent')->count(),
            'good_count' => $scores->where('rating', 'good')->count(),
            'satisfactory_count' => $scores->where('rating', 'satisfactory')->count(),
            'needs_improvement_count' => $scores->where('rating', 'needs_improvement')->count(),
        ];

        // Create report record
        $report = Report::create([
            'report_type' => 'performance_evaluation',
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'user_id' => $validated['staff_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'file_format' => 'html',
            'generated_by' => auth()->id(),
            'generated_at' => now(),
            'total_records' => $scores->count(),
            'summary' => "Performance evaluation report - Avg: " . round($stats['average_score'], 2),
        ]);

        return view('reports.performance-evaluation', compact('report', 'scores', 'stats', 'validated'));
    }

    /**
     * Generate task force performance report (R3.4.4)
     */
    public function generateTaskForceReport(Request $request)
    {
        $validated = $request->validate([
            'task_force_id' => 'required|exists:task_forces,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:1,2,annual',
        ]);

        $taskForce = TaskForce::with(['departments.staff'])->findOrFail($validated['task_force_id']);

        // Get all staff in task force departments
        $staffIds = $taskForce->departments->flatMap->staff->pluck('id')->unique();

        // Get workload submissions
        $submissions = WorkloadSubmission::whereIn('staff_id', $staffIds)
            ->where('academic_year', $validated['academic_year'])
            ->when($validated['semester'] !== 'annual', function ($q) use ($validated) {
                $q->where('semester', $validated['semester']);
            })
            ->with('staff.department', 'items')
            ->get();

        // Get performance scores
        $scores = PerformanceScore::whereIn('staff_id', $staffIds)
            ->byYear($validated['academic_year'])
            ->when($validated['semester'] !== 'annual', function ($q) use ($validated) {
                $q->bySemester($validated['semester']);
            })
            ->with('staff.department')
            ->get();

        // Calculate statistics
        $stats = [
            'total_staff' => $staffIds->count(),
            'total_submissions' => $submissions->count(),
            'completed_submissions' => $submissions->where('status', 'approved')->count(),
            'pending_submissions' => $submissions->where('status', 'submitted')->count(),
            'total_workload_hours' => $submissions->sum('total_hours'),
            'average_performance' => $scores->avg('overall_score'),
            'departments_involved' => $taskForce->departments->count(),
        ];

        // Create report record
        $report = Report::create([
            'report_type' => 'task_force_performance',
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'task_force_id' => $taskForce->id,
            'file_format' => 'html',
            'generated_by' => auth()->id(),
            'generated_at' => now(),
            'total_records' => $submissions->count() + $scores->count(),
            'summary' => "Task Force report for {$taskForce->name} - {$stats['total_staff']} staff members",
        ]);

        return view('reports.task-force-performance', compact('report', 'taskForce', 'submissions', 'scores', 'stats'));
    }

    /**
     * Show report generation form
     */
    public function create()
    {
        $staff = User::active()->whereNotNull('department_id')->orderBy('first_name')->get();
        $departments = Department::orderBy('name')->get();
        $taskForces = TaskForce::active()->orderBy('name')->get();
        $academicYears = ['2024/2025', '2025/2026'];

        return view('reports.create', compact('staff', 'departments', 'taskForces', 'academicYears'));
    }

    /**
     * Download report file
     */
    public function download(Report $report)
    {
        if ($report->file_path && Storage::exists($report->file_path)) {
            // Log download (R3.10.4)
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'report_downloaded',
                'model_type' => Report::class,
                'model_id' => $report->id,
                'new_values' => json_encode(['report_type' => $report->report_type]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);


            return Storage::download($report->file_path, $report->file_name);
        }

        return redirect()->back()->with('error', 'Report file not found');
    }

    /**
     * Export workload submissions to Excel (R3.8.1, R3.8.2)
     */
    public function exportWorkloadExcel(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'nullable|in:1,2,annual',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'nullable|in:draft,submitted,approved,rejected',
        ]);

        $query = WorkloadSubmission::query()
            ->byYear($validated['academic_year']);

        if (!empty($validated['semester']) && $validated['semester'] !== 'annual') {
            $query->bySemester($validated['semester']);
        }

        if (!empty($validated['department_id'])) {
            $query->byDepartment($validated['department_id']);
        }

        if (!empty($validated['status'])) {
            $query->byStatus($validated['status']);
        }

        $fileName = 'workload-submissions-' . $validated['academic_year'] . '-' . now()->format('YmdHis') . '.xlsx';

        // Log export
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'workload_exported',
            'model_type' => WorkloadSubmission::class,
            'new_values' => json_encode(['format' => 'excel', 'filters' => $validated]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Excel::download(new WorkloadSubmissionsExport($query, 'Workload ' . $validated['academic_year']), $fileName);
    }

    /**
     * Export performance scores to Excel (R3.8.3)
     */
    public function exportPerformanceExcel(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'nullable|in:1,2,annual',
            'department_id' => 'nullable|exists:departments,id',
            'rating' => 'nullable|in:excellent,good,satisfactory,needs_improvement',
        ]);

        $query = PerformanceScore::query()
            ->byYear($validated['academic_year']);

        if (!empty($validated['semester']) && $validated['semester'] !== 'annual') {
            $query->bySemester($validated['semester']);
        }

        if (!empty($validated['department_id'])) {
            $query->whereHas('staff', function ($q) use ($validated) {
                $q->where('department_id', $validated['department_id']);
            });
        }

        if (!empty($validated['rating'])) {
            $query->byRating($validated['rating']);
        }

        $fileName = 'performance-scores-' . $validated['academic_year'] . '-' . now()->format('YmdHis') . '.xlsx';

        // Log export
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'performance_exported',
            'model_type' => PerformanceScore::class,
            'new_values' => json_encode(['format' => 'excel', 'filters' => $validated]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Excel::download(new PerformanceScoresExport($query, 'Performance ' . $validated['academic_year']), $fileName);
    }

    /**
     * Export workload submissions to CSV (R3.8.1)
     */
    public function exportWorkloadCsv(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'nullable|in:1,2,annual',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'nullable|in:draft,submitted,approved,rejected',
        ]);

        $query = WorkloadSubmission::query()
            ->byYear($validated['academic_year']);

        if (!empty($validated['semester']) && $validated['semester'] !== 'annual') {
            $query->bySemester($validated['semester']);
        }

        if (!empty($validated['department_id'])) {
            $query->byDepartment($validated['department_id']);
        }

        if (!empty($validated['status'])) {
            $query->byStatus($validated['status']);
        }

        $fileName = 'workload-submissions-' . $validated['academic_year'] . '-' . now()->format('YmdHis') . '.csv';

        // Log export
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'workload_exported',
            'model_type' => WorkloadSubmission::class,
            'new_values' => json_encode(['format' => 'csv', 'filters' => $validated]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Excel::download(new WorkloadSubmissionsExport($query, 'Workload ' . $validated['academic_year']), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }
}
