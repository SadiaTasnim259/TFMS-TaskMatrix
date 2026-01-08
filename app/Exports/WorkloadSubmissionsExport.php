<?php

namespace App\Exports;

use App\Models\WorkloadSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class WorkloadSubmissionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $query;
    protected $title;

    public function __construct($query, $title = 'Workload Submissions')
    {
        $this->query = $query;
        $this->title = $title;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->query->with('staff.department', 'submittedBy', 'approvedBy')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Staff Name',
            'Staff ID',
            'Department',
            'Academic Year',
            'Semester',
            'Total Hours',
            'Total Credits',
            'Status',
            'Submitted By',
            'Submitted At',
            'Approved By',
            'Approved At',
            'Notes',
        ];
    }

    /**
     * @param WorkloadSubmission $submission
     * @return array
     */
    public function map($submission): array
    {
        return [
            $submission->id,
            $submission->staff->fullName(),
            $submission->staff->staff_id,
            $submission->staff->department->name ?? 'N/A',
            $submission->academic_year,
            $submission->semester,
            $submission->total_hours,
            $submission->total_credits,
            ucfirst($submission->status),
            $submission->submittedBy->name ?? 'N/A',
            $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : 'N/A',
            $submission->approvedBy->name ?? 'N/A',
            $submission->approved_at ? $submission->approved_at->format('Y-m-d H:i') : 'N/A',
            $submission->notes ?? '',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return substr($this->title, 0, 31); // Excel sheet title max 31 chars
    }
}
