<?php

namespace App\Policies;

use App\Models\TaskForce;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskForcePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view list, but content might be filtered
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskForce $taskForce): bool
    {
        if ($user->isAdmin() || $user->isPSM() || $user->hasRole('management')) {
            return true;
        }

        if ($user->isHOD()) {
            // HOD can view task forces assigned to their department
            // Assuming taskForce has departments relationship
            return $taskForce->departments->contains($user->staff->department_id);
        }

        // Lecturers can view task forces they are members of
        // Assuming taskForce has members relationship (not implemented yet, but placeholder)
        // return $taskForce->members->contains($user->staff->id);
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskForce $taskForce): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskForce $taskForce): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskForce $taskForce): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskForce $taskForce): bool
    {
        return false;
    }
}
