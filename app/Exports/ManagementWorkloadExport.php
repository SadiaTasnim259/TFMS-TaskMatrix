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

        return User::with('department')
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
