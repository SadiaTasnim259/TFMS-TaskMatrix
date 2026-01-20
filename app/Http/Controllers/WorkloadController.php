<?php

namespace App\Http\Controllers;

use App\Models\WorkloadSubmission;
use App\Models\WorkloadItem;
use App\Models\Staff;
use App\Models\AuditLog;
use App\Services\WorkloadService;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkloadController extends Controller
{
    /**
     * Show workload submissions list
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', WorkloadSubmission::class);
        $user = Auth::user();

        // Get the staff member associated with this user
        $staffMember = $user->staff;

        // Build query based on role
        $query = WorkloadSubmission::query();

        // Filter based on user role
        if ($user->isLecturer() || $user->isPSM()) {
            // Lecturers and PSM can only see their own
            if ($staffMember) {
                $query->where('staff_id', $staffMember->id);
            } else {
                return redirect('/dashboard')->with('error', 'No staff record found for your account');
            }
        } elseif ($user->isHOD()) {
            // HOD can see their department's submissions
            $departmentId = $staffMember?->department_id;
            if ($departmentId) {
                $query->byDepartment($departmentId);
            }
        }
        // Admin can see all

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->byStatus($request->status);
        }

        if ($request->has('year') && $request->year) {
            $query->byYear($request->year);
        }

        // Pagination
        $submissions = $query->with('staff.department', 'approvedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Dynamic years from DB
        $years = \App\Models\AcademicSession::distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        $statuses = ['draft', 'submitted', 'approved', 'rejected'];

        // Fetch assigned task forces for the staff member
        $assignedTaskForces = collect();
        $calculatedTotalWeightage = 0;

        if ($staffMember) {
            $assignedTaskForces = $staffMember->taskForces()
                ->where('active', true)
                ->withPivot('role')
                ->get();

            $calculatedTotalWeightage = $staffMember->calculateTotalWorkload();
        }

        return view('workload.index', compact('submissions', 'years', 'statuses', 'assignedTaskForces', 'calculatedTotalWeightage'));
    }

    /**
     * Show workload summary/status page (Dedicated View)
     */
    public function summary(WorkloadService $workloadService)
    {
        $user = Auth::user();

        // Get active task forces
        $taskForces = $user->taskForces()
            ->where('active', true)
            ->withPivot('role')
            ->get();

        // Calculate metrics
        $totalWorkload = $user->calculateTotalWorkload();
        $status = $workloadService->calculateStatus($totalWorkload);
        $statusColor = $workloadService->getStatusColor($status);

        // Get thresholds for visualization (progress bars etc)
        $minWeightage = (float) (Configuration::where('config_key', 'min_weightage')->value('config_value') ?? 10);
        $maxWeightage = (float) (Configuration::where('config_key', 'max_weightage')->value('config_value') ?? 20);

        return view('workload.summary', compact(
            'taskForces',
            'totalWorkload',
            'status',
            'statusColor',
            'minWeightage',
            'maxWeightage'
        ));
    }

    /**
     * Show form to submit workload remarks to HOD.
     */
    public function remarksForm()
    {
        return view('workload.remarks');
    }

    /**
     * Process remarks submission.
     */
    public function submitRemarks(Request $request)
    {
        $request->validate([
            'remarks' => 'required|string|min:10|max:1000',
        ]);

        $user = Auth::user();

        // Find all HODs for this user's department
        // We look for users in the same department who have the 'HOD' role (slug: 'hod')
        $hods = \App\Models\User::where('department_id', $user->department_id)
            ->whereHas('role', function ($q) {
                $q->where('slug', 'hod');
            })
            ->get();

        if ($hods->isNotEmpty()) {
            foreach ($hods as $hod) {
                \Illuminate\Support\Facades\Mail::to($hod->email)
                    ->send(new \App\Mail\WorkloadRemarksMail($user, $request->remarks));
            }
        } else {
            // Fallback: If no HOD found via role, try the department head_id link
            $deptHead = $user->department->head ?? null;
            if ($deptHead) {
                \Illuminate\Support\Facades\Mail::to($deptHead->email)
                    ->send(new \App\Mail\WorkloadRemarksMail($user, $request->remarks));
            }
        }

        return redirect()->route('workload.remarks')->with('success', 'Your remarks have been submitted for consideration.');
    }

    /**
     * Show historical records view.
     */
    public function history(Request $request, WorkloadService $workloadService)
    {
        $user = Auth::user();

        // Get years where user has tasks
        $availableYears = $user->taskForces()
            ->select('task_forces.academic_year')
            ->distinct()
            ->orderBy('task_forces.academic_year', 'desc')
            ->pluck('task_forces.academic_year');

        $selectedYear = $request->get('year');
        $taskForces = collect();
        $totalWorkload = 0;
        $status = null;
        $statusColor = null;

        // Thresholds
        $minWeightage = (float) (Configuration::where('config_key', 'min_weightage')->value('config_value') ?? 10);
        $maxWeightage = (float) (Configuration::where('config_key', 'max_weightage')->value('config_value') ?? 20);

        if ($selectedYear) {
            $taskForces = $user->taskForces()
                ->where('academic_year', $selectedYear)
                ->withPivot('role')
                ->get();

            // Calculate manual total for that specific year (can't use User::calculateTotalWorkload as it checks 'active')
            $totalWorkload = $taskForces->sum('default_weightage');

            $status = $workloadService->calculateStatus($totalWorkload);
            $statusColor = $workloadService->getStatusColor($status);
        }

        return view('workload.history', compact(
            'availableYears',
            'selectedYear',
            'taskForces',
            'totalWorkload',
            'status',
            'statusColor',
            'minWeightage',
            'maxWeightage'
        ));
    }

    /**
     * Download workload summary as PDF.
     */
    public function downloadSummaryPdf(WorkloadService $workloadService)
    {
        $user = Auth::user();

        // Calculate workload
        $totalWorkload = $user->calculateTotalWorkload();
        $status = $workloadService->calculateStatus($totalWorkload);

        // Thresholds
        $minWeightage = (float) (Configuration::where('config_key', 'min_weightage')->value('config_value') ?? 10);
        $maxWeightage = (float) (Configuration::where('config_key', 'max_weightage')->value('config_value') ?? 20);

        // Get active task forces
        $taskForces = $user->taskForces()
            ->where('active', true)
            ->withPivot('role')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('workload.pdf_summary', compact(
            'user',
            'totalWorkload',
            'status',
            'minWeightage',
            'maxWeightage',
            'taskForces'
        ));

        return $pdf->download('workload_summary.pdf');
    }

    /**
     * Show assigned task forces for the current user.
     */
    public function assignedTaskForces()
    {
        $user = Auth::user();

        // Get current academic session
        $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();
        $currentYear = $currentSession ? $currentSession->academic_year : null;

        $taskForces = $user->taskForces()
            ->where('active', true)
            ->when($currentYear, function ($query) use ($currentYear) {
                $query->where('academic_year', $currentYear);
            })
            ->withPivot('role')
            ->latest()
            ->get();

        return view('workload.assigned_task_forces', compact('taskForces', 'currentSession'));
    }

    /**
     * Show form to create new workload submission
     */
    public function create()
    {
        $this->authorize('create', WorkloadSubmission::class);
        $user = Auth::user();
        $staffMember = $user->staff;

        if (!$staffMember) {
            return redirect('/dashboard')->with('error', 'No staff record found');
        }

        $activeSession = \App\Models\AcademicSession::where('is_active', true)->first();
        $currentYear = $activeSession ? $activeSession->academic_year : date('Y') . '/' . (date('Y') + 1);

        // Check if already has a draft for active year
        $existing = WorkloadSubmission::where('staff_id', $staffMember->id)
            ->where('academic_year', $currentYear)
            ->where('status', 'draft')
            ->first();

        if ($existing) {
            return redirect()->route('workload.edit', $existing);
        }

        return view('workload.create', [
            'activeSession' => $activeSession,
            'currentYear' => $currentYear
        ]);
    }

    /**
     * Store new workload submission
     */
    public function store(Request $request)
    {
        $this->authorize('create', WorkloadSubmission::class);
        $user = Auth::user();
        $staffMember = $user->staff;

        if (!$staffMember) {
            return redirect('/dashboard')->with('error', 'No staff record found');
        }

        $activeSession = \App\Models\AcademicSession::where('is_active', true)->first();
        $defaultYear = $activeSession ? $activeSession->academic_year : date('Y') . '/' . (date('Y') + 1);
        $defaultSemester = $activeSession ? $activeSession->semester : 1;

        $submission = WorkloadSubmission::create([
            'staff_id' => $staffMember->id,
            'academic_year' => $request->input('academic_year', $defaultYear),
            'semester' => $request->input('semester', $defaultSemester),
            'status' => 'draft',
        ]);

        return redirect()->route('workload.edit', $submission)
            ->with('success', 'Workload submission created. Add activities below.');
    }

    /**
     * Edit workload submission
     */
    public function edit(WorkloadSubmission $submission)
    {
        $this->authorize('update', $submission);

        $submission->load('items', 'staff');
        $activityTypes = [
            'teaching' => 'Teaching',
            'research' => 'Research',
            'admin' => 'Administrative',
            'student_support' => 'Student Support',
            'committee_work' => 'Committee Work',
            'course_development' => 'Course Development',
            'marking_assessment' => 'Marking & Assessment',
        ];

        return view('workload.edit', compact('submission', 'activityTypes'));
    }

    /**
     * Add activity to submission
     */
    public function addActivity(Request $request, WorkloadSubmission $submission)
    {
        $this->authorize('update', $submission);

        $validated = $request->validate([
            'activity_type' => 'required|in:teaching,research,admin,student_support,committee_work,course_development,marking_assessment',
            'activity_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hours_allocated' => 'required|numeric|min:0.5|max:40',
            'credits_value' => 'required|numeric|min:0.5|max:10',
            'student_count' => 'nullable|integer|min:1',
            'course_code' => 'nullable|string|max:20',
            'semester' => 'nullable|integer|in:1,2',
            'notes' => 'nullable|string',
        ]);

        $submission->items()->create($validated);
        $submission->recalculateTotals();

        return redirect()->route('workload.edit', $submission)
            ->with('success', 'Activity added successfully');
    }

    /**
     * Remove activity from submission
     */
    public function removeActivity(WorkloadSubmission $submission, WorkloadItem $item)
    {
        $this->authorize('update', $submission);

        if ($item->workload_submission_id !== $submission->id) {
            return redirect()->back()->with('error', 'Activity not found');
        }

        $item->delete();
        $submission->recalculateTotals();

        return redirect()->back()->with('success', 'Activity removed');
    }

    /**
     * Submit workload for approval
     */
    public function submit(Request $request, WorkloadSubmission $submission)
    {
        $this->authorize('submit', $submission);

        if (!$submission->canSubmit()) {
            return redirect()->back()->with('error', 'Cannot submit workload. It may be locked or empty.');
        }

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'status' => 'submitted',
            'submitted_by' => auth()->id(),
            'submitted_at' => now(),
            'lecturer_remarks' => $request->remarks,
        ]);

        // Log submission (R3.10.1)
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'workload_submitted',
            'model_type' => WorkloadSubmission::class,
            'model_id' => $submission->id,
            'changes' => json_encode([
                'academic_year' => $submission->academic_year,
                'semester' => $submission->semester,
                'total_hours' => $submission->total_hours,
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('workload.index')
            ->with('success', 'Workload submitted for approval');
    }

    /**
     * Approve submission (Admin/HOD only)
     */
    public function approve(Request $request, WorkloadSubmission $submission)
    {
        $this->authorize('approve', $submission);

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $submission->approve(auth()->id(), $validated['notes'] ?? null);

        // Log approval (R3.10.2)
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'workload_approved',
            'model_type' => WorkloadSubmission::class,
            'model_id' => $submission->id,
            'old_values' => json_encode(['status' => 'submitted']),
            'new_values' => json_encode(['status' => 'approved', 'notes' => $validated['notes']]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Workload approved');
    }

    /**
     * Reject submission (Admin/HOD only)
     */
    public function reject(Request $request, WorkloadSubmission $submission)
    {
        $this->authorize('reject', $submission);

        $validated = $request->validate([
            'notes' => 'required|string|min:10',
        ]);

        $submission->reject(auth()->id(), $validated['notes']);

        // Log rejection (R3.10.2)
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'workload_rejected',
            'model_type' => WorkloadSubmission::class,
            'model_id' => $submission->id,
            'old_values' => json_encode(['status' => 'submitted']),
            'new_values' => json_encode(['status' => 'rejected', 'notes' => $validated['notes']]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Workload rejected with feedback');
    }

    /**
     * Show workload detail view
     */
    public function show(WorkloadSubmission $submission)
    {
        $this->authorize('view', $submission);
        $submission->load('items', 'staff');

        return view('workload.show', compact('submission'));
    }
}

