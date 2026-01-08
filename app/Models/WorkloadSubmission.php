<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkloadSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'academic_year',
        'semester',
        'total_hours',
        'total_credits',
        'status',
        'notes',
        'lecturer_remarks',
        'submitted_by',
        'approved_by',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_hours' => 'decimal:2',
        'total_credits' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the staff member who submitted this workload
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who submitted this
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who approved this
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all workload items in this submission
     */
    public function items()
    {
        return $this->hasMany(WorkloadItem::class);
    }

    /**
     * Get workload status label (Under-loaded, Balanced, Overloaded)
     */
    public function getWorkloadStatusAttribute()
    {
        $service = new \App\Services\WorkloadService();
        return $service->calculateStatus($this->total_hours);
    }

    /**
     * Get workload status color class
     */
    public function getWorkloadStatusColorAttribute()
    {
        $service = new \App\Services\WorkloadService();
        return $service->getStatusColor($this->workload_status);
    }

    // ========== SCOPES ==========

    /**
     * Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filter by academic year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Filter by semester
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Get pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Get approved submissions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Filter by department through staff relationship
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->whereHas('staff', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });
    }

    /**
     * Filter by staff
     */
    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    // ========== METHODS ==========

    /**
     * Calculate total hours from items
     */
    public function recalculateTotals()
    {
        $this->total_hours = $this->items()->sum('hours_allocated');
        $this->total_credits = $this->items()->sum('credits_value');
        $this->save();

        return $this;
    }

    /**
     * Check if can be edited
     */
    public function canEdit()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    /**
     * Check if can be submitted
     */
    public function canSubmit()
    {
        return $this->status === 'draft' && $this->items->count() > 0;
    }

    /**
     * Submit the workload
     */
    public function submit($userId)
    {
        if (!$this->canSubmit()) {
            return false;
        }

        $this->status = 'submitted';
        $this->submitted_by = $userId;
        $this->submitted_at = now();
        $this->save();

        return true;
    }

    /**
     * Approve the submission
     */
    public function approve($userId, $notes = null)
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->notes = $notes;
        $this->save();

        return true;
    }

    /**
     * Reject the submission
     */
    public function reject($userId, $notes)
    {
        $this->status = 'rejected';
        $this->approved_by = $userId;
        $this->notes = $notes;
        $this->save();

        return true;
    }

    /**
     * Get activity breakdown
     */
    public function getActivityBreakdown()
    {
        return $this->items()
            ->selectRaw('activity_type, COUNT(*) as count, SUM(hours_allocated) as total_hours')
            ->groupBy('activity_type')
            ->pluck('total_hours', 'activity_type');
    }

    /**
     * Check if all required activities are present
     */
    public function isComplete()
    {
        $requiredActivities = ['teaching', 'research', 'admin'];
        $submittedActivities = $this->items()
            ->pluck('activity_type')
            ->unique()
            ->toArray();

        return count(array_intersect($requiredActivities, $submittedActivities)) === count($requiredActivities);
    }
    /**

    /**
     * Search by staff name (R3.9.5)
     */
    public function scopeSearchByName($query, $search)
    {
        return $query->whereHas('staff', function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
        });
    }
}
