<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'user_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'description',
        'model_name',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * Get the user who performed this action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeByAction($query, $action)
    {
        if (!$action) {
            return $query;
        }

        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        if (!$userId) {
            return $query;
        }

        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by model type.
     */
    public function scopeByModel($query, $modelType)
    {
        if (!$modelType) {
            return $query;
        }

        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Get all logs for a specific entity.
     */
    public function scopeForEntity($query, $modelType, $modelId)
    {
        return $query->where('model_type', $modelType)
                     ->where('model_id', $modelId);
    }

    /**
     * Get the difference between old and new values.
     */
    public function getChangesAttribute()
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $changes = [];
        
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Format the old values as readable text.
     */
    public function getFormattedOldValuesAttribute()
    {
        return $this->formatValues($this->old_values);
    }

    /**
     * Format the new values as readable text.
     */
    public function getFormattedNewValuesAttribute()
    {
        return $this->formatValues($this->new_values);
    }

    /**
     * Helper to format values for display.
     */
    private function formatValues($values)
    {
        if (!$values) {
            return 'N/A';
        }

        $formatted = [];
        foreach ($values as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }

        return implode(', ', $formatted);
    }

    /**
     * Get a human-readable action description.
     */
    public function getActionLabelAttribute()
    {
        $labels = [
            'CREATE' => 'Created',
            'UPDATE' => 'Updated',
            'DELETE' => 'Deleted',
            'DEACTIVATE' => 'Deactivated',
            'REACTIVATE' => 'Reactivated',
            'PASSWORD_RESET' => 'Password Reset',
            'ROLE_CHANGE' => 'Role Changed',
            'LOCK' => 'Account Locked',
            'UNLOCK' => 'Account Unlocked',
        ];

        return $labels[$this->action] ?? $this->action;
    }

    /**
     * Get a human-readable model type.
     */
    public function getModelLabelAttribute()
    {
        $labels = [
            'Staff' => 'Staff Member',
            'TaskForce' => 'Task Force',
            'Configuration' => 'System Configuration',
            'Department' => 'Department',
            'User' => 'User Account',
        ];

        return $labels[$this->model_type] ?? $this->model_type;
    }

    /**
     * Create an audit log entry.
     */
    public static function log($action, $modelType, $modelId, $oldValues = [], $newValues = [], $description = null, $modelName = null)
    {
        return static::create([
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'user_id' => auth()->id() ?? 1,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'model_name' => $modelName,
            'status' => 'completed',
        ]);
    }

    /**
     * Get recent logs for dashboard.
     */
    public static function getRecentLogs($limit = 10)
    {
        return static::latest('created_at')
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get logs for a specific user.
     */
    public static function getUserLogs($userId, $limit = 50)
    {
        return static::where('user_id', $userId)
                     ->latest('created_at')
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get summary statistics.
     */
    public static function getSummaryStats($startDate = null, $endDate = null)
    {
        $query = static::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return [
            'total_actions' => $query->count(),
            'total_creates' => (clone $query)->where('action', 'CREATE')->count(),
            'total_updates' => (clone $query)->where('action', 'UPDATE')->count(),
            'total_deletes' => (clone $query)->where('action', 'DELETE')->count(),
            'actions_by_type' => (clone $query)->groupBy('action')->selectRaw('action, count(*) as count')->get(),
            'actions_by_model' => (clone $query)->groupBy('model_type')->selectRaw('model_type, count(*) as count')->get(),
        ];
    }
}
