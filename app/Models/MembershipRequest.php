<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_force_id',
        'user_id',
        'action', // 'add', 'remove'
        'role',
        'status', // 'pending', 'approved', 'rejected'
        'requested_by',
        'processed_by',
        'remarks',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function taskForce()
    {
        return $this->belongsTo(TaskForce::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
