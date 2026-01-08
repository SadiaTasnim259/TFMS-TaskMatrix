<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsSnapshot;
use App\Models\WorkloadSubmission;
use App\Models\PerformanceScore;
use App\Models\User;
use App\Models\Department;
use App\Models\WorkloadItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Show analytics dashboard
     */
    public function index()
    {
        $this->authorize('viewAny', AnalyticsSnapshot::class);

        // Get latest snapshot or calculate live data
        $snapshot = AnalyticsSnapshot::latest()->first();

        // If no snapshot, create one
        if (!$snapshot) {
            $this->generateSnapshot();
            $snapshot = AnalyticsSnapshot::latest()->first();
        }

        // Get recent submissions
        $recentSubmissions = WorkloadSubmission::with('staff.department')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get top performers
        $topPerformers = PerformanceScore::with('staff')
            ->where('academic_year', '2024/2025')
            ->orderBy('overall_score', 'desc')
            ->limit(10)
            ->get();

        // Activity breakdown
        $activityBreakdown = WorkloadItem::select('activity_type', DB::raw('COUNT(*) as count, SUM(hours_allocated) as total_hours'))
            ->groupBy('activity_type')
            ->get();

        // Department comparison
        $departmentStats = Department::select('departments.*')
            ->withCount([
                'staff' => function ($q) {
                    $q->where('active', true);
                }
            ])
            ->with([
                'staff' => function ($q) {
                    $q->whereHas('workloadSubmissions', function ($sq) {
                        $sq->where('status', 'approved');
                    });
                }
            ])
            ->get();

        return view('analytics.index', compact(
            'snapshot',
            'recentSubmissions',
            'topPerformers',
            'activityBreakdown',
            'departmentStats'
        ));
    }

    /**
     * Generate analytics snapshot
     */
    public function generateSnapshot()
    {
        $this->authorize('create', AnalyticsSnapshot::class);

        $today = now()->toDateString();

        // Check if today's snapshot already exists
        $existing = AnalyticsSnapshot::where('snapshot_date', $today)->first();
        if ($existing) {
            return redirect()->back()->with('info', 'Snapshot already exists for today');
        }

        // Calculate metrics
        $totalSubmissions = WorkloadSubmission::count();
        $completedSubmissions = WorkloadSubmission::where('status', 'approved')->count();
        $pendingApprovals = WorkloadSubmission::where('status', 'submitted')->count();

        $avgWorkloadHours = WorkloadSubmission::where('status', 'approved')->avg('total_hours') ?? 0;
        $maxWorkloadHours = WorkloadSubmission::where('status', 'approved')->max('total_hours') ?? 0;
        $minWorkloadHours = WorkloadSubmission::where('status', 'approved')->where('total_hours', '>', 0)->min('total_hours') ?? 0;

        $teachingActivities = WorkloadItem::where('activity_type', 'teaching')->count();
        $researchActivities = WorkloadItem::where('activity_type', 'research')->count();
        $adminActivities = WorkloadItem::where('activity_type', 'admin')->count();

        $avgPerformanceScore = PerformanceScore::avg('overall_score') ?? 0;
        $highPerformers = PerformanceScore::where('overall_score', '>', 80)->count();
        $lowPerformers = PerformanceScore::where('overall_score', '<', 50)->count();

        $participatingDepartments = Department::whereHas('staff.workloadSubmissions')->distinct()->count('id');
        $participatingStaff = User::whereHas('workloadSubmissions')->distinct()->count('id');

        // Create snapshot
        $snapshot = AnalyticsSnapshot::create([
            'snapshot_date' => $today,
            'snapshot_time' => now()->toTimeString(),
            'total_submissions' => $totalSubmissions,
            'completed_submissions' => $completedSubmissions,
            'pending_approvals' => $pendingApprovals,
            'average_workload_hours' => round($avgWorkloadHours, 2),
            'max_workload_hours' => round($maxWorkloadHours, 2),
            'min_workload_hours' => round($minWorkloadHours, 2),
            'teaching_activities' => $teachingActivities,
            'research_activities' => $researchActivities,
            'admin_activities' => $adminActivities,
            'average_performance_score' => round($avgPerformanceScore, 2),
            'high_performers' => $highPerformers,
            'low_performers' => $lowPerformers,
            'participating_departments' => $participatingDepartments,
            'participating_staff' => $participatingStaff,
        ]);

        return redirect()->back()->with('success', 'Analytics snapshot generated successfully');
    }

    /**
     * Show snapshot history
     */
    public function snapshots()
    {
        $this->authorize('viewAny', AnalyticsSnapshot::class);

        $snapshots = AnalyticsSnapshot::orderBy('snapshot_date', 'desc')->paginate(30);

        return view('analytics.snapshots', compact('snapshots'));
    }
}
