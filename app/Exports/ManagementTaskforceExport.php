<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManagementTaskforceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $currentYear;

    public function __construct()
    {
        $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();
        $this->currentYear = $currentSession ? $currentSession->academic_year : null;
    }

    public function collection()
    {
        $currentYear = $this->currentYear;

        return Department::withCount([
            'taskForces' => function ($query) use ($currentYear) {
                $query->where('task_forces.active', true);
                if ($currentYear) {
                    $query->where('task_forces.academic_year', $currentYear);
                }
            }
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Department',
            'Active Task Forces',
        ];
    }

    public function map($department): array
    {
        return [
            $department->name,
            $department->task_forces_count,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
