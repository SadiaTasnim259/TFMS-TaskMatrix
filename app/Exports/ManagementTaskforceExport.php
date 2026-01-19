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
    public function collection()
    {
        return Department::withCount([
            'taskForces' => function ($query) {
                $query->where('task_forces.active', true);
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
