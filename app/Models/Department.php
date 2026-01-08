<?php

namespace App\Models;

use App\Models\User; // Added this line as per the instruction's implied change
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'head_id',
        'email',
        'phone',
        'location',
        'budget',
        'active',
        'workload_status',
        'workload_submitted_at',
        'workload_locked',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
        'budget' => 'decimal:2',
        'workload_submitted_at' => 'datetime',
        'workload_locked' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the head of this department.
     * A department may have one head (staff member).
     */
    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    /**
     * Get all staff members in this department.
     */
    public function staff()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all task forces assigned to this department.
     */
    public function taskForces()
    {
        return $this->belongsToMany(
            TaskForce::class,
            'task_force_departments',
            'department_id',
            'task_force_id'
        )->withPivot('assigned_by', 'assigned_at');
    }

    /**
     * Get all workload assignments for this department.
     * (For Module 3 integration)
     */
    public function workloadAssignments()
    {
        return $this->hasManyThrough(
            'App\Models\WorkloadAssignment',
            User::class,
            'department_id',
            'staff_id'
        );
    }

    // ========== SCOPES ==========

    /**
     * Scope to get only active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to search by name or code.
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%");
    }

    /**
     * Scope to get departments by budget range.
     */
    public function scopeByBudget($query, $min = null, $max = null)
    {
        if ($min) {
            $query->where('budget', '>=', $min);
        }
        if ($max) {
            $query->where('budget', '<=', $max);
        }

        return $query;
    }

    // ========== METHODS ==========

    /**
     * Get the head's full name.
     */
    public function getHeadNameAttribute()
    {
        return $this->head ? $this->head->full_name : 'No Head Assigned';
    }

    /**
     * Get total number of active staff in this department.
     */
    public function getTotalActiveStaffAttribute()
    {
        return $this->staff()->where('active', true)->count();
    }

    /**
     * Get total workload for this department.
     * (For Module 3)
     */
    public function getTotalWorkloadAttribute()
    {
        return $this->staff()
            ->where('active', true)
            ->sum('total_workload') ?? 0;
    }

    /**
     * Get average workload per staff member.
     */
    public function getAverageWorkloadAttribute()
    {
        $activeStaff = $this->getTotalActiveStaffAttribute();

        if ($activeStaff === 0) {
            return 0;
        }

        return round($this->getTotalWorkloadAttribute() / $activeStaff, 2);
    }

    /**
     * Activate this department.
     */
    public function activate()
    {
        return $this->update(['active' => true]);
    }

    /**
     * Deactivate this department.
     */
    public function deactivate()
    {
        return $this->update(['active' => false]);
    }

    /**
     * Assign a head to this department.
     */
    public function assignHead(Staff $staff)
    {
        // Verify staff is active
        if (!$staff->active) {
            throw new \Exception('Cannot assign inactive staff as department head');
        }

        // Remove head from other departments
        Department::where('head_id', $staff->id)->update(['head_id' => null]);

        // Assign new head
        return $this->update(['head_id' => $staff->id]);
    }

    /**
     * Remove the head from this department.
     */
    public function removeHead()
    {
        return $this->update(['head_id' => null]);
    }

    /**
     * Get all task forces assigned to this department.
     */
    public function getAssignedTaskForcesAttribute()
    {
        return $this->taskForces()->active()->get();
    }

    /**
     * Check if a specific task force is assigned.
     */
    public function hasTaskForce(TaskForce $taskForce)
    {
        return $this->taskForces()->where('task_force_id', $taskForce->id)->exists();
    }

    // ========== EVENTS ==========

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Create audit log entry on create
        static::created(function ($department) {
            AuditLog::create([
                'action' => 'CREATE',
                'model_type' => 'Department',
                'model_id' => $department->id,
                'user_id' => auth()->id() ?? 1,
                'old_values' => [],
                'new_values' => $department->toArray(),
            ]);
        });

        // Create audit log entry on update
        static::updated(function ($department) {
            AuditLog::create([
                'action' => 'UPDATE',
                'model_type' => 'Department',
                'model_id' => $department->id,
                'user_id' => auth()->id() ?? 1,
                'old_values' => $department->getOriginal(),
                'new_values' => $department->getAttributes(),
            ]);
        });
    }
}
