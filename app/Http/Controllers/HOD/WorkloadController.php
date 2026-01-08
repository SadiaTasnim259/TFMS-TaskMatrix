<?php

namespace App\Http\Controllers\HOD;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Configuration;
use App\Services\WorkloadService;
use Illuminate\Http\Request;

class WorkloadController extends Controller
{
    protected $workloadService;

    public function __construct(WorkloadService $workloadService)
    {
        $this->workloadService = $workloadService;
    }

    /**
     * Display a listing of the staff workload.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user->department_id) {
            abort(403, 'Access denied. You are not associated with any department.');
        }

        $departmentId = $user->department_id;

        // Fetch thresholds once
        $minWeightage = (float) (Configuration::where('config_key', 'min_weightage')->value('config_value') ?? 10);
        $maxWeightage = (float) (Configuration::where('config_key', 'max_weightage')->value('config_value') ?? 20);

        $departmentStaff = User::where('department_id', $departmentId)
            ->where('is_active', true)
            ->with([
                'taskForces' => function ($q) {
                    $q->where('active', true);
                }
            ])
            ->orderBy('first_name')
            ->get();

        // Calculate workload for each staff and gather stats
        $totalWorkload = 0;
        $workloadValues = [];
        $statusCounts = [
            'Under-loaded' => 0,
            'Balanced' => 0,
            'Overloaded' => 0,
        ];

        $departmentStaff->each(function ($s) use ($minWeightage, $maxWeightage, &$totalWorkload, &$workloadValues, &$statusCounts) {
            $s->total_workload = $s->calculateTotalWorkload();
            $s->workload_status = $this->workloadService->calculateStatus($s->total_workload, $minWeightage, $maxWeightage);
            $s->status_color = $this->workloadService->getStatusColor($s->workload_status);

            // Aggregate stats
            $totalWorkload += $s->total_workload;
            $workloadValues[] = $s->total_workload;

            if (isset($statusCounts[$s->workload_status])) {
                $statusCounts[$s->workload_status]++;
            }
        });

        // Calculate Average
        $staffCount = $departmentStaff->count();
        $averageWorkload = $staffCount > 0 ? $totalWorkload / $staffCount : 0;

        // Calculate Standard Deviation (Fairness Score)
        $variance = 0;
        if ($staffCount > 0) {
            foreach ($workloadValues as $val) {
                $variance += pow($val - $averageWorkload, 2);
            }
            $standardDeviation = sqrt($variance / $staffCount);
        } else {
            $standardDeviation = 0;
        }

        $stats = [
            'total_workload' => $totalWorkload,
            'average_workload' => round($averageWorkload, 2),
            'fairness_score' => round($standardDeviation, 2),
            'status_counts' => $statusCounts,
            'staff_count' => $staffCount
        ];

        return view('hod.workload.index', compact('departmentStaff', 'minWeightage', 'maxWeightage', 'stats'));
    }

    /**
     * Display detailed workload for a specific staff member.
     *
     * @param  \App\Models\User  $staff
     * @return \Illuminate\View\View
     */
    public function show(User $staff)
    {
        // Authorization check
        if ($staff->department_id !== auth()->user()->department_id) {
            abort(403, 'Access denied.');
        }

        $staff->load([
            'taskForces' => function ($q) {
                $q->where('active', true);
            }
        ]);

        $totalWorkload = $staff->calculateTotalWorkload();
        $status = $this->workloadService->calculateStatus($totalWorkload);
        $statusColor = $this->workloadService->getStatusColor($status);

        return view('hod.workload.show', compact('staff', 'totalWorkload', 'status', 'statusColor'));
    }

    // submitToPSM method removed as Department-level locking is no longer required.
}
