@extends('hod.layouts.app')

@section('title', 'Staff Workload Details')

@section('content')

    <div class="mb-4">
        <a href="{{ route('hod.workload.index') }}" class="text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to Overview
        </a>
    </div>

    <div class="row">
        <!-- Staff Profile & Summary -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                    </div>
                    <h4 class="card-title">{{ $staff->first_name }} {{ $staff->last_name }}</h4>
                    <p class="text-muted mb-1">{{ $staff->email }}</p>
                    <p class="badge bg-secondary">{{ $staff->grade ?? 'N/A' }}</p>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="mb-0">{{ $totalWorkload }}</h5>
                            <small class="text-muted">Total Weightage</small>
                        </div>
                        <div class="col-6">
                            @php
                                $badgeClass = match ($status) {
                                    'Under-loaded' => 'bg-warning text-dark',
                                    'Balanced' => 'bg-success',
                                    'Overloaded' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                            <div class="small text-muted mt-1">Status</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lecturer Remarks (UC-500.3) -->
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-comment-alt me-2"></i> Lecturer Remarks</h5>
                </div>
                <div class="card-body">
                    @php
                        // Assuming $staff->workloadSubmissions or similar is available. 
                        // However, this controller 'App\Http\Controllers\HOD\WorkloadController' logic needs check.
                        // The 'show' method takes 'Staff $staff'. We need to find the relevant submission.
                        // Based on previous file reads, it calculates total workload but didn't explicitly pass a 'submission' object.
                        // Let's check if we can fetch the latest text or if we need to update the controller first.
                        // Wait, I should verify what $staff has or if I need to load the submission.
                        // The 'show' method in HOD/WorkloadController calculates workload but doesn't seem to pass a 'submission' variable.
                        // I'll check HOD/WorkloadController first.
                        $currentSession = \App\Models\Configuration::where('config_key', 'current_session')->value('config_value');
                        $currentSubmission = \App\Models\WorkloadSubmission::where('user_id', $staff->id)
                            ->where('academic_year', $currentSession)
                            ->latest()
                            ->first();
                    @endphp

                    @if($currentSubmission && $currentSubmission->lecturer_remarks)
                        <p class="fst-italic">"{{ $currentSubmission->lecturer_remarks }}"</p>
                        <small class="text-muted">Submitted:
                            {{ $currentSubmission->submitted_at ? $currentSubmission->submitted_at->diffForHumans() : 'N/A' }}</small>
                    @else
                        <p class="text-muted small fst-italic">No remarks submitted by lecturer.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Task Force List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Assigned Task Forces</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task Force</th>
                                <th>Role</th>
                                <th>Weightage</th>
                                <th>Assigned</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($staff->taskForces as $tf)
                                <tr>
                                    <td>
                                        <a href="{{ route('hod.task-forces.show', $tf->id) }}"
                                            class="text-decoration-none fw-bold">
                                            {{ $tf->name }}
                                        </a>
                                        <div class="small text-muted">{{ $tf->category }}</div>
                                    </td>
                                    <td>{{ $tf->pivot->role }}</td>
                                    <td>{{ $tf->default_weightage }}</td>
                                    <td>{{ $tf->pivot->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No active task forces assigned.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end fw-bold">Total:</td>
                                <td class="fw-bold">{{ $totalWorkload }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection