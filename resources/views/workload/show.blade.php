@extends('layouts.app')

@section('title', 'Workload Submission Details')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Workload Submission Details</h1>
                <nav aria-label="breadcrumb">

                </nav>
            </div>
            <div class="col-md-4 text-end">
                @if($submission->canEdit())
                    <a href="{{ route('workload.edit', $submission) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                <a href="{{ route('workload.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>



        <div class="row">
            <!-- Left Column: Details -->
            <div class="col-md-8">
                <!-- Submission Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Submission Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Academic Year:</strong><br>
                                <span class="fs-5">{{ $submission->academic_year }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Semester:</strong><br>
                                <span class="fs-5">Semester {{ $submission->semester }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong><br>
                                @if($submission->status == 'draft')
                                    <span class="badge bg-secondary fs-6">
                                        <i class="fas fa-edit"></i> Draft
                                    </span>
                                @elseif($submission->status == 'submitted')
                                    <span class="badge bg-warning fs-6">
                                        <i class="fas fa-clock"></i> Pending Approval
                                    </span>
                                @elseif($submission->status == 'approved')
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check"></i> Approved
                                    </span>
                                @elseif($submission->status == 'rejected')
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-times"></i> Rejected
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Created:</strong><br>
                                {{ $submission->created_at->format('M d, Y H:i') }}
                            </div>
                            @if($submission->submitted_at)
                                <div class="col-md-6 mb-3">
                                    <strong>Submitted At:</strong><br>
                                    {{ $submission->submitted_at->format('M d, Y H:i') }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Submitted By:</strong><br>
                                    {{ $submission->submittedBy->name ?? 'N/A' }}
                                </div>
                            @endif
                            @if($submission->approved_at)
                                <div class="col-md-6 mb-3">
                                    <strong>Approved At:</strong><br>
                                    {{ $submission->approved_at->format('M d, Y H:i') }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Approved By:</strong><br>
                                    {{ $submission->approvedBy->name ?? 'N/A' }}
                                </div>
                            @endif
                            @if($submission->notes)
                                <div class="col-md-12">
                                    <strong>Notes:</strong><br>
                                    <div class="alert alert-info">{{ $submission->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Activities -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i> Activities</h5>
                    </div>
                    <div class="card-body">
                        @if($submission->items->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Activity Name</th>
                                            <th>Hours</th>
                                            <th>Credits</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($submission->items as $item)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-{{ $item->getActivityTypeColor() }}">
                                                        {{ $item->getActivityTypeLabel() }}
                                                    </span>
                                                </td>
                                                <td>{{ $item->activity_name }}</td>
                                                <td><strong>{{ number_format($item->hours_allocated, 2) }}</strong> hrs</td>
                                                <td><strong>{{ number_format($item->credits_value, 2) }}</strong></td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $item->description ?: 'No description' }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th>{{ number_format($submission->total_hours, 2) }} hrs</th>
                                            <th>{{ number_format($submission->total_credits, 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No activities added</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary & Actions -->
            <div class="col-md-4">
                <!-- Summary Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted">Total Hours</small>
                            <h2 class="text-primary mb-0">{{ number_format($submission->total_hours, 2) }}</h2>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted">Total Credits</small>
                            <h2 class="text-success mb-0">{{ number_format($submission->total_credits, 2) }}</h2>
                        </div>
                        <div>
                            <small class="text-muted">Activities Count</small>
                            <h2 class="text-info mb-0">{{ $submission->items->count() }}</h2>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                @if(auth()->user()->isAdmin() || auth()->user()->isHOD())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cog"></i> Actions</h5>
                        </div>
                        <div class="card-body">
                            @if($submission->status == 'submitted')
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#approveSubmissionModal">
                                        <i class="fas fa-check me-2"></i> Approve Assignment
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectSubmissionModal">
                                        <i class="fas fa-times me-2"></i> Reject Assignment
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-info mb-0">
                                    <small>
                                        @if($submission->status == 'draft')
                                            This submission is still in draft status.
                                        @elseif($submission->status == 'approved')
                                            This submission has been approved.
                                        @elseif($submission->status == 'rejected')
                                            This submission has been rejected.
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Approve Submission Modal -->
    <div class="modal fade" id="approveSubmissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Approve Workload Submission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('workload.approve', $submission) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to approve this workload submission?</p>
                        <div class="mb-3">
                            <label class="form-label">Approval Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="Add any comments regarding this approval..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i> Confirm Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Submission Modal -->
    <div class="modal fade" id="rejectSubmissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Workload Submission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('workload.reject', $submission) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to reject this workload submission?</p>
                        <div class="mb-3">
                            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="notes" class="form-control" rows="3" required
                                placeholder="Please explain why this submission is being rejected..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i> Confirm Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection