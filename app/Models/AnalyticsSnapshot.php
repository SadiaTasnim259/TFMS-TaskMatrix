<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnalyticsSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'snapshot_date',
        'snapshot_time',
        'total_submissions',
        'completed_submissions',
        'pending_approvals',
        'average_workload_hours',
        'max_workload_hours',
        'min_workload_hours',
        'teaching_activities',
        'research_activities',
        'admin_activities',
        'average_performance_score',
        'high_performers',
        'low_performers',
        'participating_departments',
        'participating_staff',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'average_workload_hours' => 'decimal:2',
        'max_workload_hours' => 'decimal:2',
        'min_workload_hours' => 'decimal:2',
        'average_performance_score' => 'decimal:2',
    ];

    // ========== SCOPES ==========

    public function scopeLatest($query)
    {
        return $query->orderBy('snapshot_date', 'desc')->first();
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('snapshot_date', $date);
    }
}
