<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkloadSubmission;
use Illuminate\Auth\Access\Response;

class WorkloadSubmissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Filtered in controller
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkloadSubmission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can view
        if ($user->staff && $user->staff->id === $submission->staff_id) {
            return true;
        }

        // HOD can view department submissions
        if ($user->isHOD() && $user->staff && $user->staff->department_id === $submission->staff->department_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->staff && $user->staff->active;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkloadSubmission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can update if draft or returned
        if ($user->staff && $user->staff->id === $submission->staff_id) {
            return in_array($submission->status, ['draft', 'returned']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkloadSubmission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can delete if draft
        if ($user->staff && $user->staff->id === $submission->staff_id) {
            return $submission->status === 'draft';
        }

        return false;
    }

    /**
     * Determine whether the user can submit the model.
     */
    public function submit(User $user, WorkloadSubmission $submission): bool
    {
        return $this->update($user, $submission);
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, WorkloadSubmission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // HOD can approve if submitted
        if ($user->isHOD() && $user->staff && $user->staff->department_id === $submission->staff->department_id) {
            return $submission->status === 'submitted';
        }

        return false;
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, WorkloadSubmission $submission): bool
    {
        return $this->approve($user, $submission);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkloadSubmission $workloadSubmission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkloadSubmission $workloadSubmission): bool
    {
        return false;
    }
}
