<?php

namespace App\Exports;

use App\Models\User;
use App\Services\WorkloadService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManagementWorkloadExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $workloadService;

    public function __construct()
    {
        $this->workloadService = new WorkloadService();
    }

    public function collection()
    {
        return User::with('department')
            ->withSum([
                'taskForces' => function ($q) {
                    $q->where('task_forces.active', true);
                }
            ], 'default_weightage')
            ->where('is_active', true)
            ->whereNotNull('department_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Staff Name',
            'Staff ID',
            'Department',
            'Total Workload',
            'Status',
        ];
    }

    public function map($user): array
    {
        $workload = $user->task_forces_sum_default_weightage ?? 0;
        $status = $this->workloadService->calculateStatus($workload);

        return [
            $user->name,
            $user->staff_id ?? 'N/A',
            $user->department->name ?? 'N/A',
            $workload,
            $status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
