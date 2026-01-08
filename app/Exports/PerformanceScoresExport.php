<?php

namespace App\Exports;

use App\Models\PerformanceScore;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PerformanceScoresExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $query;
    protected $title;

    public function __construct($query, $title = 'Performance Scores')
    {
        $this->query = $query;
        $this->title = $title;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->query->with('staff.department', 'evaluatedBy')->get();
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
            'Teaching Score',
            'Research Score',
            'Admin Score',
            'Support Score',
            'Overall Score',
            'Rating',
            'Evaluated By',
            'Evaluated At',
            'Comments',
        ];
    }

    /**
     * @param PerformanceScore $score
     * @return array
     */
    public function map($score): array
    {
        return [
            $score->id,
            $score->staff->fullName(),
            $score->staff->staff_id,
            $score->staff->department->name ?? 'N/A',
            $score->academic_year,
            $score->semester,
            $score->teaching_score,
            $score->research_score,
            $score->admin_score,
            $score->support_score,
            $score->overall_score,
            ucfirst(str_replace('_', ' ', $score->rating)),
            $score->evaluatedBy->name ?? 'N/A',
            $score->evaluated_at ? $score->evaluated_at->format('Y-m-d H:i') : 'N/A',
            $score->comments ?? '',
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
