<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkloadItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'workload_submission_id',
        'activity_type',
        'activity_name',
        'description',
        'hours_allocated',
        'credits_value',
        'student_count',
        'course_code',
        'semester',
        'notes',
    ];

    protected $casts = [
        'hours_allocated' => 'decimal:2',
        'credits_value' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the workload submission this belongs to
     */
    public function submission()
    {
        return $this->belongsTo(WorkloadSubmission::class, 'workload_submission_id');
    }

    // ========== SCOPES ==========

    /**
     * Filter by activity type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    // ========== METHODS ==========

    /**
     * Get human-readable activity type
     */
    public function getActivityTypeLabel()
    {
        $labels = [
            'teaching' => 'Teaching',
            'research' => 'Research',
            'admin' => 'Administrative',
            'student_support' => 'Student Support',
            'committee_work' => 'Committee Work',
            'course_development' => 'Course Development',
            'marking_assessment' => 'Marking & Assessment',
        ];

        return $labels[$this->activity_type] ?? $this->activity_type;
    }

    /**
     * Get activity type badge color
     */
    public function getActivityTypeColor()
    {
        $colors = [
            'teaching' => 'primary',
            'research' => 'success',
            'admin' => 'warning',
            'student_support' => 'info',
            'committee_work' => 'secondary',
            'course_development' => 'purple',
            'marking_assessment' => 'danger',
        ];

        return $colors[$this->activity_type] ?? 'secondary';
    }
}
