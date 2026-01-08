<?php

namespace App\Http\Controllers\PSM;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Configuration;
use App\Models\User;
use App\Services\WorkloadService;
use Illuminate\Http\Request;

use App\Models\AuditLog;

class WorkloadController extends Controller
{
    protected $workloadService;

    public function __construct(WorkloadService $workloadService)
    {
        $this->workloadService = $workloadService;
    }

    /**
     * Display a listing of all departments and their workload status.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $departments = Department::withCount(['staff', 'taskForces'])
            ->with(['head'])
            ->orderBy('name')
            ->get();

        // Calculate stats for dashboard summary
        $stats = [
            'total_departments' => $departments->count(),
            'submitted' => $departments->where('workload_locked', true)->count(),
            'pending' => $departments->where('workload_locked', false)->count(),
        ];

        return view('psm.workload.index', compact('departments', 'stats'));
    }

    /**
     * Display a report of workload imbalances across the faculty.
     *
     * @return \Illuminate\View\View
     */
    public function imbalanceReport()
    {
        // Fetch thresholds
        $minWeightage = (float) (Configuration::where('config_key', 'min_weightage')->value('config_value') ?? 10);
        $maxWeightage = (float) (Configuration::where('config_key', 'max_weightage')->value('config_value') ?? 20);

        // Get all active staff (users) with their task forces and department
        $allStaff = User::active() // Using scope
            ->whereNotNull('department_id') // Ensure they are staff
            ->with([
                'department',
                'taskForces' => function ($q) {
                    $q->where('active', true);
                }
            ])
            ->get();

        $imbalancedStaff = $allStaff->map(function ($staff) use ($minWeightage, $maxWeightage) {
            $staff->total_workload = $staff->calculateTotalWorkload();
            $staff->workload_status = $this->workloadService->calculateStatus($staff->total_workload, $minWeightage, $maxWeightage);
            $staff->status_color = $this->workloadService->getStatusColor($staff->workload_status);
            return $staff;
        })->filter(function ($staff) {
            return $staff->workload_status !== 'Balanced';
        })->sortByDesc('total_workload'); // Sort by highest workload first

        $stats = [
            'overloaded' => $imbalancedStaff->where('workload_status', 'Overloaded')->count(),
            'underloaded' => $imbalancedStaff->where('workload_status', 'Under-loaded')->count(),
        ];

        return view('psm.workload.imbalance', compact('imbalancedStaff', 'stats', 'minWeightage', 'maxWeightage'));
    }

    /**
     * Display detailed workload for a specific department.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\View\View
     */
    public function show(Department $department)
    {
        $department->load([
            'staff.taskForces' => function ($q) {
                $q->where('active', true);
            }
        ]);

        // Fetch thresholds
        $minWeightage = (float) (Configuration::where('config_key', 'min_weightage')->value('config_value') ?? 10);
        $maxWeightage = (float) (Configuration::where('config_key', 'max_weightage')->value('config_value') ?? 20);

        // Calculate workload for each staff
        $department->staff->each(function ($s) use ($minWeightage, $maxWeightage) {
            $s->total_workload = $s->calculateTotalWorkload();
            $s->workload_status = $this->workloadService->calculateStatus($s->total_workload, $minWeightage, $maxWeightage);
            $s->status_color = $this->workloadService->getStatusColor($s->workload_status);
        });

        return view('psm.workload.show', compact('department', 'minWeightage', 'maxWeightage'));
    }

    /**
     * Approve the department's workload submission.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Department $department)
    {
        if (!$department->workload_locked) {
            return back()->with('error', 'Cannot approve. The workload has not been submitted yet.');
        }

        $department->update([
            'workload_status' => 'Approved',
            // You might want to add an 'approved_at' timestamp column in a future migration
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve_workload',
            'model_type' => Department::class,
            'model_id' => $department->id,
            'new_values' => json_encode(['workload_status' => 'Approved']),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('psm.workload.index')
            ->with('success', "Department {$department->name} workload approved successfully.");
    }

    /**
     * Reject the department's workload submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, Department $department)
    {
        $department->update([
            'workload_locked' => false,
            'workload_status' => 'Rejected',
            'workload_submitted_at' => null,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_workload',
            'model_type' => Department::class,
            'model_id' => $department->id,
            'new_values' => json_encode(['workload_status' => 'Rejected']),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('psm.workload.index')
            ->with('success', "Department {$department->name} workload rejected. HOD can now make changes.");
    }
}
