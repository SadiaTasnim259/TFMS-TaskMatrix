<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Configuration;
use App\Services\WorkloadService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PSMImbalanceExport implements FromCollection, WithHeadings, WithStyles
{
    protected $workloadService;
    protected $minWeightage;
    protected $maxWeightage;

    public function __construct()
    {
        $this->workloadService = new WorkloadService();
        $this->minWeightage = (float) (Configuration::where('config_key', 'min_weightage')->value('config_value') ?? 10);
        $this->maxWeightage = (float) (Configuration::where('config_key', 'max_weightage')->value('config_value') ?? 20);
    }

    public function collection()
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

        $rows = new Collection();

        foreach ($users as $user) {
            $workload = $user->task_forces_sum_default_weightage ?? 0;
            $status = $this->workloadService->calculateStatus($workload);

            // Only include imbalanced staff (Under-loaded or Overloaded)
            if ($status !== 'Balanced') {
                $rows->push([
                    'staff_name' => $user->name,
                    'staff_id' => $user->staff_id ?? 'N/A',
                    'department' => $user->department->name ?? 'N/A',
                    'workload' => $workload,
                    'status' => $status,
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Staff Name',
            'Staff ID',
            'Department',
            'Workload',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
