<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AuditLog;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicSessionController extends Controller
{
    /**
     * Display a listing of the academic sessions.
     */
    public function index()
    {
        $sessions = AcademicSession::orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('admin.academic_sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new academic session.
     */
    public function create()
    {
        return view('admin.academic_sessions.form');
    }

    /**
     * Store a newly created academic session in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'required|integer|in:1,2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'academic_year.regex' => 'Format must be YYYY/YYYY (e.g., 2024/2025)',
        ]);

        try {
            DB::beginTransaction();

            // Check if active is requested
            $isActive = $request->has('is_active');

            if ($isActive) {
                // Deactivate all others
                AcademicSession::where('is_active', true)->update(['is_active' => false]);

                // Also update the global Configuration for backward compatibility
                Configuration::setValue('academic_year', $validated['academic_year'], auth()->id());
                Configuration::setValue('current_semester', $validated['semester'], auth()->id(), 'Integer');
                Configuration::setValue('session_start_date', $validated['start_date'], auth()->id(), 'Date');
                Configuration::setValue('session_end_date', $validated['end_date'], auth()->id(), 'Date');
            }

            $session = AcademicSession::create([
                'academic_year' => $validated['academic_year'],
                'semester' => $validated['semester'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $isActive,
            ]);

            AuditLog::log(
                'create',
                'AcademicSession',
                $session->id,
                null,
                $session->toArray(),
                "Created new academic session: {$session->academic_year} Sem {$session->semester}"
            );

            DB::commit();

            return redirect()->route('admin.academic-sessions.index')
                ->with('success', 'Academic session created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating session: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified academic session.
     */
    public function edit(AcademicSession $academicSession)
    {
        return view('admin.academic_sessions.form', compact('academicSession'));
    }

    /**
     * Update the specified academic session in storage.
     */
    public function update(Request $request, AcademicSession $academicSession)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'required|integer|in:1,2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            DB::beginTransaction();

            $oldValues = $academicSession->toArray();

            // Check if active is requested
            $isActive = $request->has('is_active');

            // If we are activating this one, deactivate others
            if ($isActive && !$academicSession->is_active) {
                AcademicSession::where('is_active', true)->update(['is_active' => false]);

                // Update global Config
                Configuration::setValue('academic_year', $validated['academic_year'], auth()->id());
                Configuration::setValue('current_semester', $validated['semester'], auth()->id(), 'Integer');
                Configuration::setValue('session_start_date', $validated['start_date'], auth()->id(), 'Date');
                Configuration::setValue('session_end_date', $validated['end_date'], auth()->id(), 'Date');
            }

            $academicSession->update([
                'academic_year' => $validated['academic_year'],
                'semester' => $validated['semester'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $isActive,
            ]);

            AuditLog::log(
                'update',
                'AcademicSession',
                $academicSession->id,
                $oldValues,
                $academicSession->toArray(),
                "Updated academic session: {$academicSession->academic_year} Sem {$academicSession->semester}"
            );

            DB::commit();

            return redirect()->route('admin.academic-sessions.index')
                ->with('success', 'Academic session updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating session: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified academic session from storage.
     */
    public function destroy(AcademicSession $academicSession)
    {
        if ($academicSession->is_active) {
            return back()->with('error', 'Cannot delete the active academic session.');
        }

        // Check for associated task forces
        $taskForceCount = \App\Models\TaskForce::where('academic_year', $academicSession->academic_year)->count();

        if ($taskForceCount > 0) {
            return back()->with('error', "Cannot delete this session. There are {$taskForceCount} task force(s) associated with {$academicSession->academic_year}. Please delete or reassign them first.");
        }

        try {
            $academicSession->delete();
            return redirect()->route('admin.academic-sessions.index')
                ->with('success', 'Academic session deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting session: ' . $e->getMessage());
        }
    }

    /**
     * Activate the specified session.
     */
    public function activate(AcademicSession $academicSession)
    {
        try {
            DB::beginTransaction();

            // Deactivate all
            AcademicSession::where('is_active', true)->update(['is_active' => false]);

            // Activate target
            $academicSession->update(['is_active' => true]);

            // Update global Config for backward compatibility
            Configuration::setValue('academic_year', $academicSession->academic_year, auth()->id());
            Configuration::setValue('current_semester', $academicSession->semester, auth()->id(), 'Integer');
            Configuration::setValue('session_start_date', $academicSession->start_date, auth()->id(), 'Date');
            Configuration::setValue('session_end_date', $academicSession->end_date, auth()->id(), 'Date');

            AuditLog::log(
                'update',
                'AcademicSession',
                $academicSession->id,
                [],
                ['is_active' => true],
                "Activated academic session: {$academicSession->academic_year} Sem {$academicSession->semester}"
            );

            DB::commit();

            return back()->with('success', 'Session activated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error activating session: ' . $e->getMessage());
        }
    }
}
