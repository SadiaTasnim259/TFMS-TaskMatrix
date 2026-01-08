@extends('layouts.app')

@section('title', 'Edit Workload Submission')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Edit Workload Submission</h1>
                <nav aria-label="breadcrumb">

                </nav>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-{{ $submission->status == 'draft' ? 'secondary' : 'warning' }} fs-6">
                    {{ ucfirst($submission->status) }}
                </span>
            </div>
        </div>



        <div class="row">
            <!-- Left Column: Submission Details & Activities -->
            <div class="col-md-8">
                <!-- Submission Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Submission Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Academic Year:</strong><br>
                                {{ $submission->academic_year }}
                            </div>
                            <div class="col-md-4">
                                <strong>Semester:</strong><br>
                                Semester {{ $submission->semester }}
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong><br>
                                <span class="badge bg-{{ $submission->status == 'draft' ? 'secondary' : 'warning' }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activities List -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i> Activities</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addActivityModal">
                            <i class="fas fa-plus"></i> Add Activity
                        </button>
                    </div>
                    <div class="card-body">
                        @if($submission->items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Hours</th>
                                            <th>Credits</th>
                                            <th width="80">Action</th>
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
                                                <td>{{ number_format($item->hours_allocated, 2) }} hrs</td>
                                                <td>{{ number_format($item->credits_value, 2) }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteActivityModal"
                                                        data-activity-name="{{ $item->activity_name }}"
                                                        data-delete-url="{{ route('workload.remove-activity', [$submission, $item]) }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
                                <p class="text-muted">No activities added yet</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addActivityModal">
                                    <i class="fas fa-plus"></i> Add Your First Activity
                                </button>
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
                        <div class="mb-3">
                            <small class="text-muted">Total Hours</small>
                            <h3 class="text-primary">{{ number_format($submission->total_hours, 2) }}</h3>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Total Credits</small>
                            <h3 class="text-success">{{ number_format($submission->total_credits, 2) }}</h3>
                        </div>
                        <div>
                            <small class="text-muted">Activities</small>
                            <h3 class="text-info">{{ $submission->items->count() }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($submission->canSubmit())
                                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                                    data-bs-target="#submitWorkloadModal">
                                    <i class="fas fa-paper-plane"></i> Submit for Approval
                                </button>
                            @endif
                            <a href="{{ route('workload.show', $submission) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> View Submission
                            </a>
                            <a href="{{ route('workload.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Activity Modal -->
    <div class="modal fade" id="addActivityModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('workload.add-activity', $submission) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus"></i> Add Activity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Activity Type <span class="text-danger">*</span></label>
                                <select name="activity_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="teaching">Teaching</option>
                                    <option value="research">Research</option>
                                    <option value="admin">Administrative</option>
                                    <option value="student_support">Student Support</option>
                                    <option value="committee_work">Committee Work</option>
                                    <option value="course_development">Course Development</option>
                                    <option value="marking_assessment">Marking & Assessment</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Activity Name <span class="text-danger">*</span></label>
                                <input type="text" name="activity_name" class="form-control" required
                                    placeholder="e.g., CSC101 - Introduction to Programming">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hours Allocated <span class="text-danger">*</span></label>
                                <input type="number" name="hours_allocated" class="form-control" step="0.5" min="0.5"
                                    max="40" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Credits Value <span class="text-danger">*</span></label>
                                <input type="number" name="credits_value" class="form-control" step="0.5" min="0.5" max="10"
                                    required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description (Optional)</label>
                                <textarea name="description" class="form-control" rows="2"
                                    placeholder="Additional details about this activity"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Activity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<!-- Delete Activity Modal -->
<div class="modal fade" id="deleteActivityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove the activity <strong id="modalActivityName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteActivityForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i> Remove Activity
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
                <p>Are you sure you want to submit this workload for approval?</p>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-lock me-2"></i>
                    You will not be able to edit this submission after it is submitted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('workload.submit', $submission) }}" method="POST">
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
        var deleteActivityModal = document.getElementById('deleteActivityModal');
        deleteActivityModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var activityName = button.getAttribute('data-activity-name');
            var deleteUrl = button.getAttribute('data-delete-url');

            var modalActivityName = deleteActivityModal.querySelector('#modalActivityName');
            var deleteForm = deleteActivityModal.querySelector('#deleteActivityForm');

            modalActivityName.textContent = activityName;
            deleteForm.action = deleteUrl;
        });
    });
</script>