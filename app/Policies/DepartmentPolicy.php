<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Department $department): bool
    {
        if ($user->isAdmin() || $user->isPSM()) {
            return true;
        }

        if ($user->isHOD()) {
            return $user->department_id === $department->id;
        }

        // Regular staff can view their own department
        return $user->department_id === $department->id;
    }
}
