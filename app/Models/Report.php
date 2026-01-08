<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type',
        'academic_year',
        'semester',
        'user_id',
        'department_id',
        'task_force_id',
        'file_path',
        'file_name',
        'file_format',
        'generated_by',
        'generated_at',
        'total_records',
        'summary',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Keep legacy accessor if needed, or just remove
    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function taskForce()
    {
        return $this->belongsTo(TaskForce::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // ========== SCOPES ==========

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('generated_at', 'desc');
    }

    // ========== GETTERS ==========

    public function getTypeLabel()
    {
        $types = [
            'staff_workload' => 'Staff Workload',
            'department_workload' => 'Department Workload',
            'performance_evaluation' => 'Performance Evaluation',
            'task_force_performance' => 'Task Force Performance',
        ];

        return $types[$this->report_type] ?? ucwords(str_replace('_', ' ', $this->report_type));
    }
}
