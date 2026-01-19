<?php

namespace App\Exports;

use App\Models\TaskForce;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PSMTaskForceListExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return TaskForce::with(['leader', 'departments', 'members'])->get();
    }

    public function headings(): array
    {
        return [
            'Task Force Name',
            'Leader',
            'Departments',
            'Members Count',
            'Status',
        ];
    }

    public function map($taskForce): array
    {
        return [
            $taskForce->name,
            $taskForce->leader->name ?? 'N/A',
            $taskForce->departments->pluck('name')->join(', ') ?: 'N/A',
            $taskForce->members->count(),
            $taskForce->active ? 'Active' : 'Inactive',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
