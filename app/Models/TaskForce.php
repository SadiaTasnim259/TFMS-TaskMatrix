<?php

namespace App\Models;

use App\Models\User; // Added this line as per instruction's implied change
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskForce extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_forces';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_force_id',
        'name',
        'academic_year',
        'description',
        'default_weightage',
        'active',
        'is_locked',
        'justification',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'is_locked' => 'boolean',
        'default_weightage' => 'decimal:2',
    ];

    /**
     * RELATIONSHIPS
     * =====================================================
     */



    /**
     * Get departments assigned to this task force.
     *
     * Many to Many through task_force_departments
     *
     * Usage: $taskForce->departments
     * Returns: Collection of departments
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'task_force_departments',
            'task_force_id',
            'department_id'
        )->withPivot('assigned_by', 'assigned_at');
    }

    /**
     * Get the user who created this task force.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this task force.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the members of this task force.
     *
     * Many to Many through task_force_members
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'task_force_members', 'task_force_id', 'user_id')
            ->withPivot('role', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Get the leader of the task force.
     * Alias for owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * SCOPES
     * =====================================================================
     */

    /**
     * Scope to get only active task forces.
     *
     * Usage: TaskForce::active()->get()
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }


    /**
     * Scope to filter by department.
     *
     * Usage: TaskForce::byDepartment(1)->get()
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $departmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->whereHas('departments', function ($subQuery) use ($departmentId) {
            $subQuery->where('department_id', $departmentId);
        });
    }

    /**
     * Check if task force is active.
     *
     * Usage: if ($taskForce->isActive()) { ... }
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active === true;
    }



    /**
     * Deactivate this task force.
     *
     * Usage: $taskForce->deactivate()
     *
     * @return bool
     */
    public function deactivate()
    {
        return $this->update(['active' => false]);
    }

    /**
     * Reactivate this task force.
     *
     * Usage: $taskForce->reactivate()
     *
     * @return bool
     */
    public function reactivate()
    {
        return $this->update(['active' => true]);
    }

    /**
     * Assign this task force to a department.
     *
     * Usage: $taskForce->assignToDepartment($dept_id, $user_id)
     *
     * @param  int  $departmentId
     * @param  int  $userId
     * @return void
     */
    public function assignToDepartment($departmentId, $userId)
    {
        // Check if already assigned
        if (!$this->departments()->where('department_id', $departmentId)->exists()) {
            $this->departments()->attach($departmentId, [
                'assigned_by' => $userId,
                'assigned_at' => now(),
            ]);
        }
    }

    /**
     * Remove task force assignment from a department.
     *
     * Usage: $taskForce->removeFromDepartment($dept_id)
     *
     * @param  int  $departmentId
     * @return int  Number of rows affected
     */
    public function removeFromDepartment($departmentId)
    {
        return $this->departments()->detach($departmentId);
    }
    /**
     * RELATIONSHIPS
     */

    public function membershipRequests()
    {
        return $this->hasMany(MembershipRequest::class);
    }

    /**
     * Get the activities (workload items) associated with this task force.
     * 
     * Since WorkloadItem links to WorkloadSubmission (which links to Staff/User),
     * and WorkloadItem also has a direct task_force_id (if designed that way)
     * OR we need to infer it. 
     * 
     * Looking at the `WorkloadItem` model (implied) or database, items are usually linked to a TF.
     * Let's assume a direct HasMany or similar.
     */
    public function activities()
    {
        // Based on typical schema for this domain:
        // Option A: Direct link in WorkloadItem table (best for reporting)
        // Option B: Indirect via Members? No, that's too weak.
        // Let's assume Option A exists due to the error implying it should exist.
        // It might be named differently in DB or Model but 'activities' is the requested relation name.
        return $this->hasMany(\App\Models\WorkloadItem::class);
    }

    /**
     * CUSTOM METHODS
     */

    /**
     * Check if task force is locked for HOD modifications
     */
    public function isLocked()
    {
        return $this->is_locked;
    }

    /**
     * Lock the task force (e.g., after PSM approval)
     */
    public function lock()
    {
        return $this->update(['is_locked' => true]);
    }

    /**
     * Unlock the task force (e.g., for exceptional modification)
     */
    public function unlock($justification = null)
    {
        return $this->update([
            'is_locked' => false,
            'justification' => $justification
        ]);
    }
}
