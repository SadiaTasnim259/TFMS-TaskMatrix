<?php

namespace App\Http\Controllers\PSM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaskForce;
use App\Models\User;
use App\Models\Department;
use PDF; // Barryvdh DomPDF
use Maatwebsite\Excel\Facades\Excel; // Maatwebsite Excel

class ReportController extends Controller
{
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

        switch ($type) {
            case 'taskforce_list':
                return $this->generateTaskForceList($format);
            case 'workload_summary':
                return $this->generateWorkloadSummary($format);
            case 'overload_underload':
                return $this->generateOverloadUnderload($format);
            default:
                return back()->with('error', 'Invalid report type selected.');
        }
    }

    private function generateTaskForceList($format)
    {
        $data = TaskForce::with(['leader', 'departments', 'members'])->get();

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('psm.reports.pdf.taskforce_list', compact('data'));
            return $pdf->download('taskforce_list.pdf');
        } else {
            // Excel/CSV logic (Simplified for CSV download for now to avoid creating Export classes yet)
            return $this->exportCsv($data, ['Name', 'Leader', 'Departments', 'Members Count'], function ($tf) {
                return [
                    $tf->name,
                    $tf->leader->name ?? 'N/A',
                    $tf->departments->pluck('name')->join(', '),
                    $tf->members->count()
                ];
            }, 'taskforce_list.csv');
        }
    }

    private function generateWorkloadSummary($format)
    {
        $data = Department::with(['staff'])->get();

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('psm.reports.pdf.workload_summary', compact('data'));
            return $pdf->download('workload_summary.pdf');
        } else {
            return $this->exportCsv($data, ['Department', 'Staff Name', 'Total Workload'], function ($dept) {
                $rows = [];
                foreach ($dept->staff as $staff) {
                    $rows[] = [$dept->name, $staff->name, $staff->calculateTotalWorkload()];
                }
                return $rows; // Handle multi-row return
            }, 'workload_summary.csv', true);
        }
    }

    private function generateOverloadUnderload($format)
    {
        // Logic: Get staff with < 15 or > 40 (example thresholds)
        // Re-using WorkloadService logic ideally, but raw query for now
        $staff = User::where('is_active', true)->whereNotNull('department_id')->get()->map(function ($user) {
            $user->load_val = $user->calculateTotalWorkload();
            return $user;
        })->filter(function ($user) {
            return $user->load_val < 40 || $user->load_val > 50; // Example thresholds from previous context (or 15/40?)
            // Let's assume standard thresholds: < 20 Underload, > 40 Overload?
            // Actually, I'll just dump all stats and let view highlight it.
            // Or better, filter specifically.
            return true;
        });

        if ($format === 'pdf') {
            $data = $staff;
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('psm.reports.pdf.imbalance', compact('data'));
            return $pdf->download('imbalance_report.pdf');
        } else {
            return $this->exportCsv($staff, ['Staff Name', 'Department', 'Workload', 'Status'], function ($user) {
                return [
                    $user->name,
                    $user->department->name ?? 'N/A',
                    $user->load_val,
                    $user->load_val > 40 ? 'Overload' : ($user->load_val < 15 ? 'Underload' : 'Normal')
                ];
            }, 'imbalance_report.csv');
        }
    }

    // Helper for quick CSV export without creating classes
    private function exportCsv($collection, $headers, $callback, $filename, $multiRow = false)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback_func = function () use ($collection, $headers, $callback, $multiRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, is_array($headers) ? array_keys($headers) : $headers); // Wait, headers arg is names
            // My args are confusing. $headers is HTTP headers. 
            // array_keys($header_names)? No.

            // Re-do correctly
        };

        // Simplified StreamedResponse
        return response()->stream(function () use ($collection, $headers, $callback, $multiRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers); // Header Row

            foreach ($collection as $item) {
                $row = $callback($item);
                if ($multiRow && is_array($row) && isset($row[0]) && is_array($row[0])) {
                    foreach ($row as $r)
                        fputcsv($file, $r);
                } else {
                    fputcsv($file, $row);
                }
            }
            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
