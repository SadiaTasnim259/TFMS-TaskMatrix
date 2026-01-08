<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskForce;
use App\Models\Department;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TaskForceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * Display a listing of task forces.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TaskForce::class);

        $query = TaskForce::with('departments');

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('task_force_id', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('active', $request->status === 'active');
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $taskForces = $query->paginate(25);
        $categories = $this->getCategories();

        return view('admin.task_forces.index', compact('taskForces', 'categories'));
    }

    /**
     * Show the form for creating a new task force.
     */
    public function create()
    {
        $this->authorize('create', TaskForce::class);

        // Active Staff fetching removed as owner_id is removed

        $academicSessions = \App\Models\AcademicSession::orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get();
        $currentSession = \App\Models\AcademicSession::where('is_active', true)->first();

        // Get configured max workload or default to 20
        $maxWorkload = (float) \App\Models\Configuration::getValue('max_weightage', 20.0);

        return view('admin.task_forces.create', compact('academicSessions', 'currentSession', 'maxWorkload'));
    }

    /**
     * Store a newly created task force in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TaskForce::class);

        // Get configured max workload or default to 20
        $maxWorkload = (float) \App\Models\Configuration::getValue('max_weightage', 20.0);

        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
            'default_weightage' => "required|numeric|min:1.0|max:{$maxWorkload}|regex:/^\d+(\.\d{1,2})?$/",
            'active' => 'boolean',
        ], [
            'default_weightage.regex' => 'Weightage must be a number with up to 2 decimal places.',
            'default_weightage.max' => "Weightage cannot exceed the configured maximum of {$maxWorkload} hours.",
        ]);

        try {
            DB::beginTransaction();

            // Generate Task Force ID
            $year = date('Y');
            $uniqueId = 'TF-' . $year . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            // Ensure uniqueness (simple check, or use do-while)
            while (TaskForce::where('task_force_id', $uniqueId)->exists()) {
                $uniqueId = 'TF-' . $year . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }

            // Create task force
            $taskForce = TaskForce::create([
                'task_force_id' => $uniqueId,
                'name' => $validated['name'],
                'academic_year' => $validated['academic_year'],
                'description' => $validated['description'] ?? null,
                'default_weightage' => $validated['default_weightage'],
                'start_date' => null, // Removed from form
                'end_date' => null,   // Removed from form
                'active' => $validated['active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            // Log audit
            AuditLog::log(
                'CREATE',
                'TaskForce',
                $taskForce->id,
                [],
                $taskForce->toArray(),
                "Created task force: {$taskForce->name}",
                $taskForce->name
            );

            DB::commit();

            return redirect()
                ->route('admin.task-forces.show', $taskForce)
                ->with('success', "Task force '{$taskForce->name}' created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error creating task force: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified task force.
     */
    public function show(TaskForce $taskForce)
    {
        $this->authorize('view', $taskForce);
        $taskForce->load('departments');
        $assignedDepartments = $taskForce->departments()->pluck('departments.id')->toArray();

        // Log access
        AuditLog::log(
            'VIEW',
            'TaskForce',
            $taskForce->id,
            [],
            [],
            "Viewed task force details: {$taskForce->name}",
            $taskForce->name
        );

        return view('admin.task_forces.show', compact('taskForce', 'assignedDepartments'));
    }

    /**
     * Show the form for editing the specified task force.
     */
    public function edit(TaskForce $taskForce)
    {
        $this->authorize('update', $taskForce);
        $taskForce->load('departments');
        $activeStaff = \App\Models\User::active()
            ->get()
            ->sortBy('full_name');
        $assignedDepartments = $taskForce->departments()->pluck('departments.id')->toArray();

        $academicSessions = \App\Models\AcademicSession::orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get(); // added next line for context


        // Get configured max workload or default to 20
        $maxWorkload = (float) \App\Models\Configuration::getValue('max_weightage', 20.0);

        return view('admin.task_forces.edit', compact('taskForce', 'activeStaff', 'assignedDepartments', 'academicSessions', 'maxWorkload'));
    }

    /**
     * Update the specified task force in storage.
     */
    public function update(Request $request, TaskForce $taskForce)
    {
        $this->authorize('update', $taskForce);

        // Get configured max workload or default to 20
        $maxWorkload = (float) \App\Models\Configuration::getValue('max_weightage', 20.0);

        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'default_weightage' => "required|numeric|min:1.0|max:{$maxWorkload}|regex:/^\d+(\.\d{1,2})?$/",
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'active' => 'boolean',
        ], [
            'default_weightage.max' => "Weightage cannot exceed the configured maximum of {$maxWorkload} hours.",
        ]);

        try {
            DB::beginTransaction();

            $oldValues = $taskForce->toArray();

            // Update task force
            $taskForce->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'default_weightage' => $validated['default_weightage'],
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'active' => $validated['active'] ?? true,
                'updated_at' => now(),
            ]);

            // Log audit
            AuditLog::log(
                'UPDATE',
                'TaskForce',
                $taskForce->id,
                $oldValues,
                $taskForce->fresh()->toArray(),
                "Updated task force: {$taskForce->name}",
                $taskForce->name
            );

            DB::commit();

            return redirect()
                ->route('admin.task-forces.show', $taskForce)
                ->with('success', "Task force '{$taskForce->name}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error updating task force: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the active status of a task force.
     */
    public function toggleStatus(TaskForce $taskForce)
    {
        $this->authorize('update', $taskForce);

        try {
            $oldActive = $taskForce->active;
            $newActive = !$oldActive;

            $taskForce->update(['active' => $newActive]);

            $action = $newActive ? 'REACTIVATE' : 'DEACTIVATE';
            $actionLabel = $newActive ? 'Reactivated' : 'Deactivated';

            AuditLog::log(
                $action,
                'TaskForce',
                $taskForce->id,
                ['active' => $oldActive],
                ['active' => $newActive],
                "{$actionLabel} task force: {$taskForce->name}",
                $taskForce->name
            );

            return back()->with('success', "Task force {$actionLabel} successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error toggling status: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for assigning departments to task force.
     */
    public function assignDepartmentsForm(TaskForce $taskForce)
    {
        $this->authorize('update', $taskForce);
        $taskForce->load('departments');
        $allDepartments = Department::active()->get();
        $assignedDepartmentIds = $taskForce->departments()->pluck('departments.id')->toArray();

        return view('admin.task_forces.assign', compact('taskForce', 'allDepartments', 'assignedDepartmentIds'));
    }

    /**
     * Assign departments to a task force.
     */
    public function assignDepartments(Request $request, TaskForce $taskForce)
    {
        $this->authorize('update', $taskForce);

        // Validate input
        $validated = $request->validate([
            'departments' => 'required|array|min:1',
            'departments.*' => [
                'integer',
                Rule::exists('departments', 'id'),
            ],
        ]);

        try {
            DB::beginTransaction();

            $currentDepartmentIds = $taskForce->departments()->pluck('departments.id')->toArray();

            // Sync departments (overwrite existing assignments with new selection)
            $taskForce->departments()->sync($validated['departments']);

            // Log audit
            AuditLog::log(
                'UPDATE',
                'TaskForce',
                $taskForce->id,
                ['departments' => $currentDepartmentIds],
                ['departments' => $validated['departments']],
                "Assigned {$taskForce->name} to " . count($validated['departments']) . " department(s)",
                $taskForce->name
            );

            DB::commit();

            return redirect()
                ->route('admin.task-forces.show', $taskForce)
                ->with('success', 'Departments assigned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error assigning departments: ' . $e->getMessage());
        }
    }

    /**
     * Remove a department from a task force.
     */
    public function removeDepartment(TaskForce $taskForce, Department $department)
    {
        $this->authorize('update', $taskForce);

        try {
            if (!$taskForce->departments()->where('department_id', $department->id)->exists()) {
                return back()->with('error', 'Department is not assigned to this task force.');
            }

            $taskForce->departments()->detach($department->id);

            AuditLog::log(
                'UPDATE',
                'TaskForce',
                $taskForce->id,
                ['department_removed' => $department->name],
                ['status' => 'department_removed'],
                "Removed {$department->name} from {$taskForce->name}",
                $taskForce->name
            );

            return back()->with('success', "Department removed from task force successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error removing department: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified task force.
     */
    public function destroy(TaskForce $taskForce)
    {
        $this->authorize('delete', $taskForce);

        try {
            DB::beginTransaction();

            $taskForceName = $taskForce->name;
            $taskForceId = $taskForce->id;

            // Detach departments
            $taskForce->departments()->detach();

            // Delete task force
            $taskForce->delete();

            // Log audit
            AuditLog::log(
                'DELETE',
                'TaskForce',
                $taskForceId,
                $taskForce->toArray(),
                [],
                "Deleted task force: {$taskForceName}",
                $taskForceName
            );

            DB::commit();

            return redirect()
                ->route('admin.task-forces.index')
                ->with('success', "Task force '{$taskForceName}' deleted successfully.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error deleting task force: ' . $e->getMessage());
        }
    }

    /**
     * Get available task force categories.
     */
    private function getCategories()
    {
        return [
            'Academic' => 'Academic',
            'Research' => 'Research',
            'Accreditation' => 'Accreditation',
            'Quality' => 'Quality Assurance',
            'Strategic' => 'Strategic Planning',
            'Administrative' => 'Administrative',
        ];
    }

    /**
     * Export task forces to CSV.
     */
    public function export()
    {
        $taskForces = TaskForce::with('departments')->get();

        $csv = "Task Force ID,Name,Category,Weightage,Active,Departments,Created Date\n";

        foreach ($taskForces as $tf) {
            $departments = $tf->departments->pluck('name')->implode('; ');
            $csv .= "\"{$tf->task_force_id}\",\"{$tf->name}\",\"{$tf->category}\",\"{$tf->default_weightage}\",";
            $csv .= ($tf->active ? 'Yes' : 'No') . ",\"{$departments}\",\"{$tf->created_at->format('Y-m-d')}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="task-forces-' . date('Y-m-d') . '.csv"');
    }
}
