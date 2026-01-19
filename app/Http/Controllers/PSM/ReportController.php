<?php

namespace App\Http\Controllers\PSM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaskForce;
use App\Models\User;
use App\Models\Department;
use App\Models\Configuration;
use App\Services\WorkloadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PSMTaskForceListExport;
use App\Exports\PSMWorkloadSummaryExport;
use App\Exports\PSMImbalanceExport;

class ReportController extends Controller
{
    protected $workloadService;

    public function __construct(WorkloadService $workloadService)
    {
        $this->workloadService = $workloadService;
    }

    /**
     * Display the report generation form.
     */
    public function index()
    {
        return view('psm.reports.index');
    }

    /**
     * Generate the selected report.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string|in:taskforce_list,workload_summary,overload_underload',
            'format' => 'required|string|in:pdf,excel',
        ]);

        $type = $request->report_type;
        $format = $request->format;
        $filename = $type . '_report_' . date('Y-m-d_H-i-s');

        // Excel/CSV export using Laravel Excel
        if ($format === 'excel') {
            $export = match ($type) {
                'taskforce_list' => new PSMTaskForceListExport(),
                'workload_summary' => new PSMWorkloadSummaryExport(),
                'overload_underload' => new PSMImbalanceExport(),
            };

            return Excel::download($export, $filename . '.csv');
        }

        // PDF export using DomPDF
        if ($format === 'pdf') {
            switch ($type) {
                case 'taskforce_list':
                    return $this->generateTaskForceListPdf($filename);
                case 'workload_summary':
                    return $this->generateWorkloadSummaryPdf($filename);
                case 'overload_underload':
                    return $this->generateImbalancePdf($filename);
            }
        }

        return back()->with('error', 'Invalid report configuration.');
    }

    private function generateTaskForceListPdf($filename)
    {
        $data = TaskForce::with(['leader', 'departments', 'members'])->get();
        $pdf = Pdf::loadView('psm.reports.pdf.taskforce_list', compact('data'));
        return $pdf->download($filename . '.pdf');
    }

    private function generateWorkloadSummaryPdf($filename)
    {
        $data = Department::with([
            'staff' => function ($query) {
                $query->where('is_active', true)
                    ->withSum([
                        'taskForces' => function ($q) {
                            $q->where('task_forces.active', true);
                        }
                    ], 'default_weightage');
            }
        ])->get();

        $workloadService = $this->workloadService;
        $pdf = Pdf::loadView('psm.reports.pdf.workload_summary', compact('data', 'workloadService'));
        return $pdf->download($filename . '.pdf');
    }

    private function generateImbalancePdf($filename)
    {
        $users = User::with('department')
            ->withSum([
                'taskForces' => function ($q) {
                    $q->where('task_forces.active', true);
                }
            ], 'default_weightage')
            ->where('is_active', true)
            ->whereNotNull('department_id')
            ->get();

        // Filter to only imbalanced staff
        $workloadService = $this->workloadService;
        $data = $users->filter(function ($user) use ($workloadService) {
            $workload = $user->task_forces_sum_default_weightage ?? 0;
            $status = $workloadService->calculateStatus($workload);
            return $status !== 'Balanced';
        });

        $pdf = Pdf::loadView('psm.reports.pdf.imbalance', compact('data', 'workloadService'));
        return $pdf->download($filename . '.pdf');
    }
}
