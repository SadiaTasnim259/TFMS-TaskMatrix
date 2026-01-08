<?php

namespace App\Policies;

use App\Models\AnalyticsSnapshot;
use App\Models\User;

class AnalyticsSnapshotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole('management');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AnalyticsSnapshot $analyticsSnapshot): bool
    {
        return $user->isAdmin() || $user->hasRole('management');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AnalyticsSnapshot $analyticsSnapshot): bool
    {
        return $user->isAdmin();
    }
}
