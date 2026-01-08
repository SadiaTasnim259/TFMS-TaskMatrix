<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'staff_id',
        'email',
        'password',
        'role_id',
        'department_id',
        'department_id',
        'employment_status',
        'notes',
        'is_active',            // matches migration
        'is_first_login',       // matches migration & controller
        'must_change_password', // Added for comprehensive enforcement
        'last_login_at',
        'failed_login_attempts',// matches migration
        'locked_until',
        'created_by',
        'updated_by',
    ];


    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_first_login' => 'boolean',
        'must_change_password' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
    ];


    // ========== RELATIONSHIPS ==========

    /**
     * Get the role this user has.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the department this user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get audit logs created by this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the task forces this user belongs to.
     */
    public function taskForces()
    {
        return $this->belongsToMany(TaskForce::class, 'task_force_members', 'user_id', 'task_force_id')
            ->withPivot('role', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Get task forces where this user is the owner/chair.
     */
    public function ownedTaskForces()
    {
        return $this->hasMany(TaskForce::class, 'owner_id');
    }

    /**
     * Get workload submissions for this user.
     */
    public function workloadSubmissions()
    {
        return $this->hasMany(\App\Models\WorkloadSubmission::class, 'user_id');
    }

    /**
     * Get performance scores for this user.
     */
    public function performanceScores()
    {
        return $this->hasMany(\App\Models\PerformanceScore::class, 'user_id');
    }

    /**
     * Get membership requests made by or for this user (as the subject).
     */
    public function membershipRequests()
    {
        return $this->hasMany(\App\Models\MembershipRequest::class, 'user_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only admin users.
     */
    public function scopeAdmin($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('name', 'Admin');
        });
    }

    /**
     * Scope to get locked users.
     */
    public function scopeLocked($query)
    {
        return $query->whereNotNull('locked_until')
            ->where('locked_until', '>', now());
    }

    /**
     * Scope to get users that must change password.
     */
    public function scopeMustChangePassword($query)
    {
        return $query->where('must_change_password', true);
    }

    /**
     * Scope to search by name or email.
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%");
    }

    // ========== METHODS ==========

    /**
     * Check if user account is locked.
     */
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until > now();
    }

    /**
     * Lock the user account.
     */
    public function lock()
    {
        return $this->update([
            'locked_until' => now()->addYears(10),
        ]);
    }

    /**
     * Unlock the user account.
     */
    public function unlock()
    {
        return $this->update([
            'locked_until' => null,
            'failed_attempts' => 0,
        ]);
    }

    /**
     * Increment failed login attempts.
     */
    public function incrementFailedAttempts()
    {
        if ($this->isAdmin()) {
            return $this;
        }

        $this->increment('failed_login_attempts');

        // Lock account after 3 failed attempts
        if ($this->failed_login_attempts >= 3) {
            $this->lock();
        }

        return $this;
    }

    /**
     * Reset failed attempts.
     */
    public function resetFailedAttempts()
    {
        return $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin()
    {
        return $this->update([
            'last_login_at' => now(),
        ]);
    }

    /**
     * Check if user has a specific role.
     * 
     * @param string $roleSlug
     * @return bool
     */
    public function hasRole($roleSlug)
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is HOD.
     */
    public function isHOD()
    {
        return $this->hasRole('hod');
    }

    /**
     * Check if user is PSM.
     */
    public function isPSM()
    {
        return $this->hasRole('psm');
    }



    /**
     * Check if user is Lecturer.
     */
    public function isLecturer()
    {
        return $this->hasRole('lecturer');
    }

    /**
     * Check if user can access admin panel.
     */
    public function canAccessAdmin()
    {
        return $this->is_active && $this->isAdmin();
    }

    /**
     * Check if user is active (alias for is_active matching Staff method).
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get user's department.
     */
    public function getDepartmentAttribute()
    {
        // Now directly on user
        return $this->department()->first();
    }

    /**
     * Get table-associated department ID (accessor for compatibility).
     */
    public function getDepartmentIdAttribute($value)
    {
        return $value;
    }

    /**
     * Check if user can submit workload.
     */
    public function canSubmitWorkload()
    {
        return $this->is_active; // Simplified as user and staff are same
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $this->name;
    }

    /**
     * Compatibility method for fullName() call.
     */
    public function fullName()
    {
        return $this->full_name;
    }

    /**
     * Scope to filter by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Calculate total workload from active task forces.
     */
    public function calculateTotalWorkload()
    {
        return $this->taskForces()
            ->where('active', true)
            ->sum('default_weightage');
    }

    /**
     * Get user's full name or email.
     */
    public function getDisplayNameAttribute()
    {
        return $this->name ?: $this->email;
    }

    /**
     * Backwards-compatible alias for resetFailedAttempts().
     * Used by AuthController::login().
     */
    /**
     * Get the label for employment status.
     */
    public function employmentStatusLabel()
    {
        return match ($this->employment_status) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'temporary' => 'Temporary',
            default => ucfirst(str_replace('_', ' ', $this->employment_status ?? 'Unknown')),
        };
    }

    public function resetLoginAttempts()
    {
        return $this->resetFailedAttempts();
    }
}
