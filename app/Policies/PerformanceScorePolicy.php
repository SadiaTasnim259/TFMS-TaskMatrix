<?php

namespace App\Policies;

use App\Models\PerformanceScore;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PerformanceScorePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isHOD() || $user->isStaff();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PerformanceScore $performanceScore): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isHOD()) {
            return $user->staff && $user->staff->department_id === $performanceScore->staff->department_id;
        }

        if ($user->isStaff()) {
            return $user->staff && $user->staff->id === $performanceScore->staff_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isHOD();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PerformanceScore $performanceScore): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isHOD()) {
            return $user->staff && $user->staff->department_id === $performanceScore->staff->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PerformanceScore $performanceScore): bool
    {
        return $user->isAdmin();
    }
}
