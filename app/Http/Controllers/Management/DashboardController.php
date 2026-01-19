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


        $departments = Department::withCount('taskForces')->get();

        // 2. Workload Fairness & Department Comparison
        $staffMembers = User::with(['taskForces', 'department'])
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
            // Calculate total weightage
            $totalWeightage = $staff->calculateTotalWorkload();

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
            'departmentStats'
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
        // 1. Fetch Departments with their Task Forces
        // We need to count task forces by category for each department.
        // Since TaskForce <-> Department is Many-to-Many

        $departments = Department::with([
            'taskForces' => function ($query) {
                // 'category' column is missing in DB despite migration. Selecting only valid columns.
                $query->select('task_forces.id', 'task_forces.active');
            }
        ])->get();

        // 2. Aggregate Data
        // Structure: ['DeptName' => ['Academic' => 5, 'Research' => 2, ...]]
        $distributionData = [];
        // Only showing 'Uncategorized' as DB is missing category column currently
        $categories = ['Uncategorized'];

        // Initialize totals for Summary Card
        $totalTaskForces = 0;

        foreach ($departments as $dept) {
            $stats = array_fill_keys($categories, 0); // Initialize all cats to 0

            foreach ($dept->taskForces as $tf) {
                if ($tf->isActive()) { // Only count active ones
                    // Fallback to 'Uncategorized' since column is missing
                    $category = $tf->category ?? 'Uncategorized';

                    if (in_array($category, $categories)) {
                        $stats[$category]++;
                    } else {
                        $stats['Uncategorized']++;
                    }
                    $totalTaskForces++;
                }
            }

            $distributionData[] = [
                'name' => $dept->name,
                'stats' => $stats,
                'total' => array_sum($stats)
            ];
        }

        return view('management.task_distribution', compact('distributionData', 'categories', 'totalTaskForces'));
    }

    public function exportReports()
    {
        return view('management.export_reports');
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

        // PDF Logic - placeholder for now
        if ($format === 'pdf') {
            return back()->with('error', 'PDF generation is currently being configured. Please use CSV for now.');
        }

        return back();
    }

    public function departmentComparison()
    {
        $departments = Department::with(['head', 'staff'])->get(); // Eager load head and staff

        $comparisonData = [];

        foreach ($departments as $dept) {
            $activeStaff = $dept->staff->where('is_active', true); // Use is_active from User model
            $staffCount = $activeStaff->count();
            $totalWorkload = 0;

            // Status Counters
            $statusCounts = [
                'Under-loaded' => 0,
                'Balanced' => 0,
                'Overloaded' => 0,
            ];

            foreach ($activeStaff as $staff) {
                $workload = $staff->calculateTotalWorkload();
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

        return view('management.department_comparison', compact('comparisonData'));
    }
}
