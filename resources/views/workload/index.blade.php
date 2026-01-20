@extends('layouts.app')

@section('title', 'My Workload Submissions')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">My Workload Dashboard</h1>
                <p class="text-muted">
                    Overview of your assigned task forces and workload status
                    @if(isset($currentSession))
                        <span class="badge bg-primary ms-2">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $currentSession->academic_year }} - Semester {{ $currentSession->semester }}
                        </span>
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-end">
                <form action="{{ route('reports.staff-workload') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="staff_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="academic_year" value="{{ $years->first() ?? '2024/2025' }}">
                    <input type="hidden" name="semester" value="annual">
                    <input type="hidden" name="format" value="pdf">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-download me-1"></i> Download Summary
                    </button>
                </form>
                <a href="{{ route('workload.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Submission
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Total Weightage</h6>
                                <h2 class="mt-2 mb-0">{{ $calculatedTotalWeightage }}</h2>
                            </div>
                            <i class="fas fa-weight-hanging fa-2x opacity-50"></i>
                        </div>
                        <small class="text-white-50">Calculated from assigned task forces</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Active Task Forces</h6>
                                <h2 class="mt-2 mb-0">{{ $assignedTaskForces->count() }}</h2>
                            </div>
                            <i class="fas fa-tasks fa-2x opacity-50"></i>
                        </div>
                        <small class="text-white-50">Currently assigned</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Submissions</h6>
                                <h2 class="mt-2 mb-0">{{ $submissions->total() }}</h2>
                            </div>
                            <i class="fas fa-file-alt fa-2x opacity-50"></i>
                        </div>
                        <small class="text-white-50">Total history records</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Task Forces Section -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="fas fa-list me-1"></i>
                <strong>Assigned Task Forces</strong>
            </div>
            <div class="card-body">
                @if($assignedTaskForces->isEmpty())
                    <p class="text-muted text-center my-3">No task forces currently assigned.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Task Force Name</th>
                                    <th>Category</th>
                                    <th>Role</th>
                                    <th>Weightage</th>
                                    <th>Dates</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignedTaskForces as $tf)
                                    <tr>
                                        <td>{{ $tf->name }}</td>
                                        <td><span class="badge bg-secondary">{{ $tf->category }}</span></td>
                                        <td>{{ $tf->pivot->role ?? 'Member' }}</td>
                                        <td>{{ $tf->default_weightage }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $tf->start_date ? $tf->start_date->format('M Y') : 'N/A' }} -
                                                {{ $tf->end_date ? $tf->end_date->format('M Y') : 'Ongoing' }}
                                            </small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('workload.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Academic Year</label>
                        <select name="year" class="form-select">
                            <option value="">All Years</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select">
                            <option value="">All Semesters</option>
                            <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                            <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted
                            </option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alert Messages -->




        <!-- Submissions Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Total Hours</th>
                                <th>Total Credits</th>
                                <th>Status</th>
                                <th>Submitted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $submission)
                                <tr>
                                    <td>{{ $submission->academic_year }}</td>
                                    <td>Semester {{ $submission->semester }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ number_format($submission->total_hours, 2) }} hrs
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ number_format($submission->total_credits, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($submission->status == 'draft')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-edit"></i> Draft
                                            </span>
                                        @elseif($submission->status == 'submitted')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        @elseif($submission->status == 'approved')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Approved
                                            </span>
                                        @elseif($submission->status == 'rejected')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i> Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'Not submitted' }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('workload.show', $submission) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($submission->canEdit())
                                                <a href="{{ route('workload.edit', $submission) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($submission->canSubmit())
                                                <button type="button" class="btn btn-sm btn-outline-success" title="Submit"
                                                    data-bs-toggle="modal" data-bs-target="#submitWorkloadModal"
                                                    data-submission-year="{{ $submission->academic_year }}"
                                                    data-submission-semester="{{ $submission->semester }}"
                                                    data-submit-url="{{ route('workload.submit', $submission) }}">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No workload submissions found</p>
                                        <a href="{{ route('workload.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Create Your First Submission
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($submissions->hasPages())
                    <div class="mt-3">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

<!-- Submit Workload Modal -->
<div class="modal fade" id="submitWorkloadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Submit Workload</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Submit workload for <strong id="modalSubmissionInfo"></strong>?</p>
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-lock me-2"></i>
                    You will not be able to edit this submission after it is submitted.
                </div>
                <!-- Remarks Section (UC-500.3) -->
                <div class="mb-3">
                    <label for="remarks" class="form-label fw-bold">Remarks for HOD (Optional)</label>
                    <textarea class="form-control" name="remarks" id="remarks" rows="3"
                        placeholder="E.g., Justification for low workload, special considerations..."></textarea>
                    <div class="form-text">These remarks will be reviewed by your Head of Department.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="submitWorkloadForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i> Yes, Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var submitWorkloadModal = document.getElementById('submitWorkloadModal');
        submitWorkloadModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var year = button.getAttribute('data-submission-year');
            var semester = button.getAttribute('data-submission-semester');
            var submitUrl = button.getAttribute('data-submit-url');

            var modalSubmissionInfo = submitWorkloadModal.querySelector('#modalSubmissionInfo');
            var submitForm = submitWorkloadModal.querySelector('#submitWorkloadForm');

            modalSubmissionInfo.textContent = year + ' - Semester ' + semester;
            submitForm.action = submitUrl;
        });
    });
</script>