<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\TaskForce;
use App\Services\WorkloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    protected $workloadService;

    public function __construct(WorkloadService $workloadService)
    {
        $this->workloadService = $workloadService;
    }

    public function index()
    {
        $data = $this->getDashboardData();
        return view('management.dashboard', $data);
    }

    public function export()
    {
        $data = $this->getDashboardData();
        $pdf = Pdf::loadView('management.pdf_report', $data);
        return $pdf->stream('management_report.pdf');
    }

    private function getDashboardData()
    {
        // Get current academic session
        $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();
        $currentYear = $currentSession ? $currentSession->academic_year : null;

        $departments = Department::withCount([
            'taskForces' => function ($query) use ($currentYear) {
                if ($currentYear) {
                    $query->where('academic_year', $currentYear);
                }
            }
        ])->get();

        // 2. Workload Fairness & Department Comparison
        // Calculate workload from task forces in current session only
        $staffMembers = User::with(['department'])
            ->withSum([
                'taskForces' => function ($q) use ($currentYear) {
                    $q->where('active', true);
                    if ($currentYear) {
                        $q->where('academic_year', $currentYear);
                    }
                }
            ], 'default_weightage')
            ->where('is_active', true)
            ->whereNotNull('department_id')
            ->get();

        $fairnessStats = [
            'Under-loaded' => 0,
            'Balanced' => 0,
            'Overloaded' => 0,
        ];

        $departmentStats = [];

        foreach ($staffMembers as $staff) {
            // Use the aggregated sum instead of calculateTotalWorkload
            $totalWeightage = $staff->task_forces_sum_default_weightage ?? 0;

            $status = $this->workloadService->calculateStatus($totalWeightage);

            if (isset($fairnessStats[$status])) {
                $fairnessStats[$status]++;
            }

            // Department Stats Accumulation
            if ($staff->department) {
                $deptId = $staff->department->id;
                if (!isset($departmentStats[$deptId])) {
                    $departmentStats[$deptId] = [
                        'id' => $deptId,
                        'name' => $staff->department->name,
                        'total_weightage' => 0,
                        'staff_count' => 0,
                        'average_weightage' => 0
                    ];
                }
                $departmentStats[$deptId]['total_weightage'] += $totalWeightage;
                $departmentStats[$deptId]['staff_count']++;
            }
        }

        // Calculate Averages
        foreach ($departmentStats as &$stat) {
            if ($stat['staff_count'] > 0) {
                $stat['average_weightage'] = round($stat['total_weightage'] / $stat['staff_count'], 2);
            }
        }

        return compact(
            'departments',
            'fairnessStats',
            'departmentStats',
            'currentSession'
        );
    }

    public function department(Department $department)
    {
        $department->load(['staff.taskForces']);

        // Calculate stats for this department
        $staffStats = $department->staff->map(function ($staff) {
            $total = $staff->calculateTotalWorkload();
            $status = $this->workloadService->calculateStatus($total);
            return [
                'staff' => $staff,
                'total_weightage' => $total,
                'status' => $status,
                'color' => $this->workloadService->getStatusColor($status)
            ];
        });

        return view('management.department', compact('department', 'staffStats'));
    }

    public function taskDistribution()
    {
        // Get current academic session
        $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();
        $currentYear = $currentSession ? $currentSession->academic_year : null;

        // Get total count of ALL active task forces from current session
        $totalTaskForces = TaskForce::where('active', true)
            ->when($currentYear, function ($query) use ($currentYear) {
                $query->where('academic_year', $currentYear);
            })
            ->count();

        // 1. Fetch Departments with their Task Forces (filtered by current session and active only)
        $departments = Department::with([
            'taskForces' => function ($query) use ($currentYear) {
                $query->select('task_forces.id', 'task_forces.active', 'task_forces.academic_year')
                    ->where('task_forces.active', true);
                if ($currentYear) {
                    $query->where('task_forces.academic_year', $currentYear);
                }
            }
        ])->get();

        // 2. Aggregate Data for chart and table
        $distributionData = [];
        $categories = ['Uncategorized'];

        foreach ($departments as $dept) {
            $stats = array_fill_keys($categories, 0);

            foreach ($dept->taskForces as $tf) {
                if ($tf->isActive()) {
                    $category = $tf->category ?? 'Uncategorized';
                    if (in_array($category, $categories)) {
                        $stats[$category]++;
                    } else {
                        $stats['Uncategorized']++;
                    }
                }
            }

            $distributionData[] = [
                'name' => $dept->name,
                'stats' => $stats,
                'total' => array_sum($stats)
            ];
        }

        // 3. Count Unassigned Task Forces (not assigned to any department)
        $unassignedQuery = TaskForce::where('active', true)
            ->doesntHave('departments');

        if ($currentYear) {
            $unassignedQuery->where('academic_year', $currentYear);
        }

        $unassignedCount = $unassignedQuery->count();

        if ($unassignedCount > 0) {
            $distributionData[] = [
                'name' => 'Unassigned',
                'stats' => ['Uncategorized' => $unassignedCount],
                'total' => $unassignedCount
            ];
        }

        return view('management.task_distribution', compact('distributionData', 'categories', 'totalTaskForces', 'currentSession'));
    }

    public function exportReports()
    {
        $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();
        return view('management.export_reports', compact('currentSession'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:workload,department,taskforce',
            'format' => 'required|in:csv,pdf',
        ]);

        $type = $request->report_type;
        $format = $request->format;
        $filename = $type . '_report_' . date('Y-m-d_H-i-s');

        // CSV/Excel generation using Laravel Excel
        if ($format === 'csv') {
            $export = match ($type) {
                'workload' => new \App\Exports\ManagementWorkloadExport(),
                'department' => new \App\Exports\ManagementDepartmentExport(),
                'taskforce' => new \App\Exports\ManagementTaskforceExport(),
            };

            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename . '.csv');
        }

        // PDF generation using DomPDF
        if ($format === 'pdf') {
            $workloadService = $this->workloadService;

            // Get current academic session
            $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();
            $currentYear = $currentSession ? $currentSession->academic_year : null;

            if ($type === 'workload') {
                $users = User::with('department')
                    ->withSum([
                        'taskForces' => function ($q) use ($currentYear) {
                            $q->where('task_forces.active', true);
                            if ($currentYear) {
                                $q->where('task_forces.academic_year', $currentYear);
                            }
                        }
                    ], 'default_weightage')
                    ->where('is_active', true)
                    ->whereNotNull('department_id')
                    ->get();

                $pdf = Pdf::loadView('management.pdf.workload', compact('users', 'workloadService', 'currentSession'));

            } elseif ($type === 'department') {
                $departments = Department::with([
                    'staff' => function ($query) use ($currentYear) {
                        $query->where('is_active', true)
                            ->withSum([
                                'taskForces' => function ($q) use ($currentYear) {
                                    $q->where('task_forces.active', true);
                                    if ($currentYear) {
                                        $q->where('task_forces.academic_year', $currentYear);
                                    }
                                }
                            ], 'default_weightage');
                    }
                ])->get();

                $pdf = Pdf::loadView('management.pdf.department', compact('departments', 'workloadService', 'currentSession'));

            } elseif ($type === 'taskforce') {
                $departments = Department::withCount([
                    'taskForces' => function ($query) use ($currentYear) {
                        $query->where('task_forces.active', true);
                        if ($currentYear) {
                            $query->where('task_forces.academic_year', $currentYear);
                        }
                    }
                ])->get();

                $pdf = Pdf::loadView('management.pdf.taskforce', compact('departments', 'currentSession'));
            }

            return $pdf->download($filename . '.pdf');
        }

        return back();
    }

    public function departmentComparison()
    {
        // Get current academic session
        $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();
        $currentYear = $currentSession ? $currentSession->academic_year : null;

        $departments = Department::with([
            'head',
            'staff' => function ($q) use ($currentYear) {
                $q->withSum([
                    'taskForces' => function ($query) use ($currentYear) {
                        $query->where('active', true);
                        if ($currentYear) {
                            $query->where('academic_year', $currentYear);
                        }
                    }
                ], 'default_weightage');
            }
        ])->get();

        $comparisonData = [];

        foreach ($departments as $dept) {
            $activeStaff = $dept->staff->where('is_active', true);
            $staffCount = $activeStaff->count();
            $totalWorkload = 0;

            // Status Counters
            $statusCounts = [
                'Under-loaded' => 0,
                'Balanced' => 0,
                'Overloaded' => 0,
            ];

            foreach ($activeStaff as $staff) {
                $workload = $staff->task_forces_sum_default_weightage ?? 0;
                $totalWorkload += $workload;

                $status = $this->workloadService->calculateStatus($workload);
                if (isset($statusCounts[$status])) {
                    $statusCounts[$status]++;
                }
            }

            $avgWorkload = $staffCount > 0 ? round($totalWorkload / $staffCount, 2) : 0;

            $comparisonData[] = [
                'id' => $dept->id,
                'name' => $dept->name,
                'head_name' => $dept->head_name, // Uses accessor
                'staff_count' => $staffCount,
                'avg_weightage' => $avgWorkload,
                'status_distribution' => $statusCounts
            ];
        }

        return view('management.department_comparison', compact('comparisonData', 'currentSession'));
    }
}
