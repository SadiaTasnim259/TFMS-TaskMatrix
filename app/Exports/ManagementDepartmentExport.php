<?php

namespace App\Exports;

use App\Models\Department;
use App\Services\WorkloadService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManagementDepartmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $workloadService;
    protected $currentYear;

    public function __construct()
    {
        $this->workloadService = new WorkloadService();
        $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();
        $this->currentYear = $currentSession ? $currentSession->academic_year : null;
    }

    public function collection()
    {
        $currentYear = $this->currentYear;

        return Department::with([
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
    }

    public function headings(): array
    {
        return [
            'Department',
            'Staff Count',
            'Avg Weightage',
            'Under-loaded',
            'Balanced',
            'Overloaded',
        ];
    }

    public function map($department): array
    {
        $staffCount = $department->staff->count();
        $totalWorkload = 0;
        $under = 0;
        $balanced = 0;
        $over = 0;

        foreach ($department->staff as $staff) {
            $workload = $staff->task_forces_sum_default_weightage ?? 0;
            $totalWorkload += $workload;

            $status = $this->workloadService->calculateStatus($workload);
            if ($status === 'Under-loaded')
                $under++;
            elseif ($status === 'Balanced')
                $balanced++;
            elseif ($status === 'Overloaded')
                $over++;
        }

        $avgWorkload = $staffCount > 0 ? round($totalWorkload / $staffCount, 2) : 0;

        return [
            $department->name,
            $staffCount,
            $avgWorkload,
            $under,
            $balanced,
            $over,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
