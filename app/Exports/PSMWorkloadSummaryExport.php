<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PSMWorkloadSummaryExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        $departments = Department::with([
            'staff' => function ($query) {
                $query->where('is_active', true)
                    ->withSum([
                        'taskForces' => function ($q) {
                            $q->where('task_forces.active', true);
                        }
                    ], 'default_weightage');
            }
        ])->get();

        $rows = new Collection();

        foreach ($departments as $dept) {
            foreach ($dept->staff as $staff) {
                $rows->push([
                    'department' => $dept->name,
                    'staff_name' => $staff->name,
                    'staff_id' => $staff->staff_id ?? 'N/A',
                    'total_workload' => $staff->task_forces_sum_default_weightage ?? 0,
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Department',
            'Staff Name',
            'Staff ID',
            'Total Workload',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
