<?php

namespace App\Http\Controllers\HOD;

use App\Http\Controllers\Controller;
use App\Models\TaskForce;
use App\Models\Configuration;
use App\Models\User;
use App\Services\WorkloadService;
use Illuminate\Http\Request;

class TaskForceController extends Controller
{
    protected $workloadService;

    public function __construct(WorkloadService $workloadService)
    {
        $this->workloadService = $workloadService;
    }

    /**
     * Display a listing of the task forces for the HOD's department.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ensure user belongs to a department
        if (!$user->department_id) {
            abort(403, 'Access denied. You are not associated with any department.');
        }

        $departmentId = $user->department_id;

        // Get current academic session
        $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();

        // Start query filtering by HOD's department
        $query = TaskForce::active()
            ->whereHas('departments', function ($q) use ($departmentId) {
                $q->where('departments.id', $departmentId);
            });

        // Filter by current academic session only
        if ($currentSession) {
            $query->where('academic_year', $currentSession->academic_year);
        }

        // Search by Name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $taskForces = $query->with([
            'members' => function ($q) use ($departmentId) {
                $q->where('users.department_id', $departmentId);
            },
            'membershipRequests' => function ($q) use ($departmentId) {
                $q->whereHas('user', function ($subQ) use ($departmentId) {
                    $subQ->where('department_id', $departmentId);
                });
            }
        ])->latest()->get();

        // Filter by Assignment Status (post-query since it requires relationship data)
        if ($request->filled('assignment_status')) {
            $status = $request->assignment_status;
            $taskForces = $taskForces->filter(function ($tf) use ($status) {
                $hasPending = $tf->membershipRequests->where('status', 'pending')->isNotEmpty();
                $isLocked = $tf->isLocked();
                $memberCount = $tf->members->count();

                switch ($status) {
                    case 'locked':
                        return $isLocked;
                    case 'pending':
                        return $hasPending && !$isLocked;
                    case 'assigned':
                        return $memberCount > 0 && !$hasPending && !$isLocked;
                    case 'not_assigned':
                        return $memberCount == 0 && !$hasPending && !$isLocked;
                    default:
                        return true;
                }
            });
        }

        // Paginate the collection manually
        $page = $request->get('page', 1);
        $perPage = 10;
        $taskForces = new \Illuminate\Pagination\LengthAwarePaginator(
            $taskForces->forPage($page, $perPage),
            $taskForces->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('hod.task_forces.index', compact('taskForces', 'currentSession'));
    }

    /**
     * Display the specified task force.
     *
     * @param  \App\Models\TaskForce  $taskForce
     * @return \Illuminate\View\View
     */
    public function show(TaskForce $taskForce)
    {
        $this->authorizeTaskForceAccess($taskForce);

        // Log access
        \App\Models\AuditLog::log(
            'VIEW',
            'TaskForce',
            $taskForce->id,
            [],
            [],
            "Viewed task force details: {$taskForce->name}",
            $taskForce->name
        );

        $taskForce->load(['members.department', 'departments']);

        $departmentId = auth()->user()->department_id;

        // Get staff (users) from the HOD's department who are NOT already members AND have no pending/draft requests
        $availableStaff = User::where('department_id', $departmentId)
            ->whereDoesntHave('taskForces', function ($q) use ($taskForce) {
                $q->where('task_forces.id', $taskForce->id);
            })
            ->whereDoesntHave('membershipRequests', function ($q) use ($taskForce) {
                // Exclude if they have a request for this TF that is pending or draft
                $q->where('task_force_id', $taskForce->id)
                    ->whereIn('status', ['pending', 'draft']);
            })
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        // Calculate workload for available staff to provide suggestions
        $availableStaff->each(function ($s) {
            $s->current_load = $s->calculateTotalWorkload();
            $s->load_status = $this->workloadService->calculateStatus($s->current_load);
        });

        // Sort by workload (lowest first) to suggest under-loaded staff
        $availableStaff = $availableStaff->sortBy('current_load');

        // Get Pending Requests (Submitted to PSM)
        $pendingRequests = \App\Models\MembershipRequest::where('task_force_id', $taskForce->id)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        // Get Draft Requests (Not yet submitted)
        $draftRequests = \App\Models\MembershipRequest::where('task_force_id', $taskForce->id)
            ->where('status', 'draft')
            ->where('requested_by', auth()->id()) // Only show own drafts
            ->with('user')
            ->get();

        return view('hod.task_forces.show', compact('taskForce', 'availableStaff', 'pendingRequests', 'draftRequests'));
    }

    /**
     * Add a member to the task force.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaskForce  $taskForce
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addMember(Request $request, TaskForce $taskForce)
    {
        $this->authorizeTaskForceAccess($taskForce);

        // Check lock status
        if ($taskForce->isLocked()) {
            return back()->with('error', 'This Task Force is locked. You cannot add members.');
        }

        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'role' => 'required|string|max:50',
        ]);

        // Verify staff belongs to HOD's department
        $staff = User::findOrFail($request->staff_id);
        if ($staff->department_id !== auth()->user()->department_id) {
            abort(403, 'You can only assign staff from your own department.');
        }

        // Create Membership Request (DRAFT)
        \App\Models\MembershipRequest::create([
            'task_force_id' => $taskForce->id,
            'user_id' => $staff->id,
            'action' => 'add',
            'role' => $request->role,
            'status' => 'draft', // <--- Changed to draft
            'requested_by' => auth()->id(),
        ]);

        // No audit log yet for draft? Or log as "Drafted"? 
        // User said "add but not save... submit to PSM". 
        // We'll log it as 'MEMBER_REQ_DRAFT' for internal tracking if needed, or just skip logging until submit.
        // Let's skip logging for draft to reduce noise, or log as 'DRAFT_ADDED'.
        // Decided: Skip heavy audit log until submission, or use a lighter one. 
        // For now, let's keep it simple and just return success.

        return back()->with('success', 'Member added to draft list. Don\'t forget to click "Submit to PSM" when done.');
    }

    /**
     * Remove a member from the task force.
     *
     * @param  \App\Models\TaskForce  $taskForce
     * @param  int  $staffId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeMember(TaskForce $taskForce, $staffId)
    {
        $this->authorizeTaskForceAccess($taskForce);

        // Check lock status
        if ($taskForce->isLocked()) {
            return back()->with('error', 'This Task Force is locked. You cannot remove members.');
        }

        $staff = \App\Models\User::find($staffId);
        $staffName = $staff ? $staff->full_name : 'Unknown Staff';

        // Create Membership Request
        \App\Models\MembershipRequest::create([
            'task_force_id' => $taskForce->id,
            'user_id' => $staffId,
            'action' => 'remove',
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);

        // Log audit
        \App\Models\AuditLog::log(
            'MEMBER_REQ_REMOVE',
            'TaskForce',
            $taskForce->id,
            ['staff_id' => $staffId],
            [],
            "Submitted request to remove {$staffName} from task force {$taskForce->name}",
            $taskForce->name
        );

        return back()->with('success', 'Member removal request submitted to PSM for approval.');
    }

    /**
     * Submit all draft requests to PSM.
     *
     * @param  \App\Models\TaskForce  $taskForce
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitRequests(TaskForce $taskForce)
    {
        $this->authorizeTaskForceAccess($taskForce);

        if ($taskForce->isLocked()) {
            return back()->with('error', 'Task Force is locked.');
        }

        // Find all draft requests for this TF created by this user
        $drafts = \App\Models\MembershipRequest::where('task_force_id', $taskForce->id)
            ->where('status', 'draft')
            ->where('requested_by', auth()->id())
            ->get();

        if ($drafts->isEmpty()) {
            return back()->with('error', 'No draft requests to submit.');
        }

        // Update status to pending and log
        foreach ($drafts as $request) {
            $request->update(['status' => 'pending']);

            // Log audit now that it's submitted
            \App\Models\AuditLog::log(
                $request->action === 'add' ? 'MEMBER_REQ_ADD' : 'MEMBER_REQ_REMOVE',
                'TaskForce',
                $taskForce->id,
                [],
                ['request_id' => $request->id, 'user_id' => $request->user_id],
                "Submitted request to {$request->action} member from task force",
                $taskForce->name
            );
        }

        return back()->with('success', 'All draft requests have been submitted to PSM.');
    }

    /**
     * Remove a draft request.
     * 
     * @param \App\Models\TaskForce $taskForce
     * @param int $requestId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteDraft(TaskForce $taskForce, $requestId)
    {
        $this->authorizeTaskForceAccess($taskForce);

        $request = \App\Models\MembershipRequest::where('id', $requestId)
            ->where('task_force_id', $taskForce->id)
            ->where('status', 'draft')
            ->where('requested_by', auth()->id())
            ->firstOrFail();

        $request->delete();

        return back()->with('success', 'Draft request removed.');
    }

    /**
     * Cancel a pending membership request (withdraw from PSM).
     *
     * @param \App\Models\TaskForce $taskForce
     * @param \App\Models\MembershipRequest $membershipRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelRequest(TaskForce $taskForce, \App\Models\MembershipRequest $membershipRequest)
    {
        $this->authorizeTaskForceAccess($taskForce);

        if ($taskForce->isLocked()) {
            return back()->with('error', 'Task Force is locked.');
        }

        // Verify request belongs to this TF and is pending and created by this user
        if ($membershipRequest->task_force_id !== $taskForce->id || $membershipRequest->status !== 'pending') {
            abort(403, 'Invalid request or status.');
        }

        if ($membershipRequest->requested_by !== auth()->id()) {
            abort(403, 'You can only cancel your own requests.');
        }

        $membershipRequest->delete();

        // Log audit
        \App\Models\AuditLog::log(
            'MEMBER_REQ_CANCEL',
            'TaskForce',
            $taskForce->id,
            ['request_id' => $membershipRequest->id],
            [],
            "Cancelled (withdrew) membership request",
            $taskForce->name
        );

        return back()->with('success', 'Request withdrawn successfully.');
    }

    /**
     * Update a pending membership request (e.g. change role).
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\TaskForce $taskForce
     * @param \App\Models\MembershipRequest $membershipRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRequest(Request $request, TaskForce $taskForce, \App\Models\MembershipRequest $membershipRequest)
    {
        $this->authorizeTaskForceAccess($taskForce);

        if ($taskForce->isLocked()) {
            return back()->with('error', 'Task Force is locked.');
        }

        // Verify request validity
        if ($membershipRequest->task_force_id !== $taskForce->id || $membershipRequest->status !== 'pending') {
            abort(403, 'Invalid request or status.');
        }

        if ($membershipRequest->requested_by !== auth()->id()) {
            abort(403, 'You can only update your own requests.');
        }

        // Only role update for 'add' requests makes sense currently
        if ($membershipRequest->action !== 'add') {
            return back()->with('error', 'Only addition requests can be modified.');
        }

        $request->validate([
            'role' => 'required|string|max:50',
        ]);

        $oldRole = $membershipRequest->role;
        $membershipRequest->update(['role' => $request->role]);

        // Log audit
        \App\Models\AuditLog::log(
            'MEMBER_REQ_UPDATE',
            'TaskForce',
            $taskForce->id,
            ['old_role' => $oldRole],
            ['new_role' => $request->role],
            "Updated pending request role for {$membershipRequest->user->first_name}",
            $taskForce->name
        );

        return back()->with('success', 'Request updated successfully.');
    }

    /**
     * Check if the HOD has access to this task force.
     *
     * @param  \App\Models\TaskForce  $taskForce
     * @return void
     */
    private function authorizeTaskForceAccess(TaskForce $taskForce)
    {
        $user = auth()->user();

        if (!$user->department_id) {
            abort(403, 'Access denied.');
        }

        // Check if task force is assigned to HOD's department
        $hasAccess = $taskForce->departments()
            ->where('departments.id', $user->department_id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'This task force is not assigned to your department.');
        }
    }
}
