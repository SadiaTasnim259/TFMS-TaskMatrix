<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Relationship: One role has many users
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role is admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->name === 'Admin';
    }

    /**
     * Check if role is HOD
     *
     * @return bool
     */
    public function isHOD(): bool
    {
        return $this->name === 'HOD';
    }

    /**
     * Check if role is PSM
     *
     * @return bool
     */
    public function isPSM(): bool
    {
        return $this->name === 'PSM';
    }
}
