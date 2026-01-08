<?php

namespace App\Http\Controllers;

use App\Models\PerformanceScore;
use App\Models\User;
use App\Models\WorkloadSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    /**
     * Show performance scores list
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', PerformanceScore::class);
        $user = Auth::user();

        $query = PerformanceScore::with('staff.department', 'evaluatedBy');

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->byYear($request->year);
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->byRating($request->rating);
        }

        // Filter by department (HOD can only see their department)
        if ($user->isHOD()) {
            if ($user->department_id) {
                $query->byDepartment($user->department_id);
            }
        }

        $scores = $query->orderBy('overall_score', 'desc')->paginate(20);

        $years = ['2024/2025', '2025/2026'];
        $ratings = ['excellent', 'good', 'satisfactory', 'needs_improvement', 'unrated'];

        return view('performance.index', compact('scores', 'years', 'ratings'));
    }

    /**
     * Show form to create/edit performance score
     */
    public function edit(User $staff, Request $request)
    {
        $year = $request->input('year', '2024/2025');
        $semester = $request->input('semester', 'annual');

        // Get or create performance score
        $score = PerformanceScore::firstOrNew([
            'staff_id' => $staff->id,
            'academic_year' => $year,
            'semester' => $semester,
        ]);

        $this->authorize('update', $score);

        // Get workload data to help with evaluation
        $workloadSubmissions = WorkloadSubmission::where('staff_id', $staff->id)
            ->where('academic_year', $year)
            ->when($semester !== 'annual', function ($q) use ($semester) {
                $q->where('semester', $semester);
            })
            ->with('items')
            ->get();

        $years = ['2024/2025', '2025/2026'];
        $semesters = ['1' => 'Semester 1', '2' => 'Semester 2', 'annual' => 'Annual'];

        return view('performance.edit', compact('score', 'staff', 'workloadSubmissions', 'years', 'semesters'));
    }

    /**
     * Save performance score
     */
    public function update(Request $request, User $staff)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'required|in:1,2,annual',
            'teaching_score' => 'required|numeric|min:0|max:100',
            'research_score' => 'required|numeric|min:0|max:100',
            'admin_score' => 'required|numeric|min:0|max:100',
            'student_support_score' => 'required|numeric|min:0|max:100',
            'teaching_weight' => 'required|numeric|min:0|max:100',
            'research_weight' => 'required|numeric|min:0|max:100',
            'admin_weight' => 'required|numeric|min:0|max:100',
            'support_weight' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string',
        ]);

        // Check authorization
        $scoreToCheck = PerformanceScore::firstOrNew([
            'staff_id' => $staff->id,
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
        ]);
        $this->authorize('update', $scoreToCheck);

        // Validate weights sum to 100
        $totalWeight = $validated['teaching_weight'] + $validated['research_weight'] +
            $validated['admin_weight'] + $validated['support_weight'];

        if ($totalWeight != 100) {
            return redirect()->back()->withErrors(['weights' => 'Weights must sum to 100%'])->withInput();
        }

        // Get or create score
        $score = PerformanceScore::updateOrCreate(
            [
                'staff_id' => $staff->id,
                'academic_year' => $validated['academic_year'],
                'semester' => $validated['semester'],
            ],
            [
                'teaching_score' => $validated['teaching_score'],
                'research_score' => $validated['research_score'],
                'admin_score' => $validated['admin_score'],
                'student_support_score' => $validated['student_support_score'],
                'teaching_weight' => $validated['teaching_weight'],
                'research_weight' => $validated['research_weight'],
                'admin_weight' => $validated['admin_weight'],
                'support_weight' => $validated['support_weight'],
                'comments' => $validated['comments'],
                'evaluated_by' => auth()->id(),
            ]
        );

        // Calculate overall score and rating
        $score->calculateOverallScore()->save();

        return redirect()->route('performance.index')
            ->with('success', 'Performance score saved successfully');
    }

    /**
     * Show individual performance detail
     */
    public function show(PerformanceScore $score)
    {
        $this->authorize('view', $score);
        $score->load('staff.department', 'evaluatedBy');

        // Get workload submissions for context
        $workloadSubmissions = WorkloadSubmission::where('staff_id', $score->staff_id)
            ->where('academic_year', $score->academic_year)
            ->when($score->semester !== 'annual', function ($q) use ($score) {
                $q->where('semester', $score->semester);
            })
            ->with('items')
            ->get();

        return view('performance.show', compact('score', 'workloadSubmissions'));
    }
}
