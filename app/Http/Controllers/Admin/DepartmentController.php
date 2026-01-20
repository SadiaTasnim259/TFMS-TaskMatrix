<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::withCount('staff')->get();
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:10|unique:departments',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        // Handle checkbox unchecked (checkboxes not sent when unchecked)
        $validated['active'] = $request->has('active');

        $department = Department::create($validated);

        AuditLog::log(
            'CREATE',
            'Department',
            $department->id,
            null,
            $department->toArray(),
            "Created department: {$department->name}",
            auth()->user()->name
        );

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'code' => ['required', 'string', 'max:10', Rule::unique('departments')->ignore($department->id)],
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $oldValues = $department->toArray();

        // Handle checkbox unchecked
        $validated['active'] = $request->has('active');

        $department->update($validated);

        AuditLog::log(
            'UPDATE',
            'Department',
            $department->id,
            $oldValues,
            $department->fresh()->toArray(),
            "Updated department: {$department->name}",
            auth()->user()->name
        );

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        if ($department->staff()->count() > 0) {
            return back()->with('error', 'Cannot delete department with assigned staff.');
        }

        $name = $department->name;
        $id = $department->id;
        $oldValues = $department->toArray();

        $department->delete();

        AuditLog::log(
            'DELETE',
            'Department',
            $id,
            $oldValues,
            null,
            "Deleted department: {$name}",
            auth()->user()->name
        );

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
