@extends('layouts.app')

@section('title', 'Review Department Workload')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
            <div>
                <h1>Review Workload: {{ $department->name }}</h1>

            </div>

            <div class="d-flex gap-2">
                @if($department->workload_locked && $department->workload_status != 'Approved')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveWorkloadModal">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectWorkloadModal">
                        <i class="fas fa-times me-1"></i> Reject
                    </button>
                @elseif($department->workload_status == 'Approved')
                    <div class="alert alert-success mb-0 py-2 px-3">
                        <i class="fas fa-check-circle me-1"></i> Approved
                    </div>
                    <!-- Option to unlock if needed -->
                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2" data-bs-toggle="modal"
                        data-bs-target="#reopenWorkloadModal">
                        Reopen
                    </button>
                @else
                    <div class="alert alert-warning mb-0 py-2 px-3">
                        <i class="fas fa-clock me-1"></i> Pending Submission from HOD
                    </div>
                @endif
            </div>
        </div>

        <!-- Modals for PSM Actions -->
        @if($department->workload_locked && $department->workload_status != 'Approved')
            <!-- Approve Modal -->
            <div class="modal fade" id="approveWorkloadModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Approve Workload Plan</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to <strong>APPROVE</strong> this workload plan for {{ $department->name }}?
                            </p>
                            <p class="text-muted small">This will finalize the workload distribution for this semester.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('psm.workload.approve', $department->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i> Confirm Approval
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectWorkloadModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Reject Workload Plan</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to <strong>REJECT</strong> this workload plan?</p>
                            <p class="text-muted">This will unlock the plan and return it to the HOD for corrections.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('psm.workload.reject', $department->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times me-2"></i> Confirm Rejection
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($department->workload_status == 'Approved')
            <!-- Reopen Modal -->
            <div class="modal fade" id="reopenWorkloadModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reopen Approved Plan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to <strong>REOPEN</strong> this approved plan?</p>
                            <p class="text-danger small">Warning: The plan is already approved. Reopening it will change its
                                status and allow editing.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('psm.workload.reject', $department->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-unlock me-2"></i> Reopen Plan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Threshold Info -->
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <div>
                <strong>Current Thresholds:</strong>
                Under-loaded (< {{ $minWeightage }}),
                    Balanced ({{ $minWeightage }} - {{ $maxWeightage }}),
                    Overloaded (> {{ $maxWeightage }})
            </div>
        </div>

        <!-- Staff Workload Table -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-users me-1"></i>
                Staff Workload Distribution
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Staff Member</th>
                            <th>Role</th>
                            <th>Active TaskForce</th>
                            <th>Total Weightage</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($department->staff as $staff)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $staff->first_name }} {{ $staff->last_name }}</div>
                                            <small class="text-muted">{{ $staff->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $staff->grade ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $staff->taskForces->count() }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $staff->total_workload }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match ($staff->workload_status) {
                                            'Under-loaded' => 'bg-warning text-dark',
                                            'Balanced' => 'bg-success',
                                            'Overloaded' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $staff->workload_status }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#staffModal{{ $staff->id }}">
                                        View
                                    </button>

                                    <!-- Modal for Staff Details -->
                                    <div class="modal fade" id="staffModal{{ $staff->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Workload Details: {{ $staff->first_name }}
                                                        {{ $staff->last_name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>Assigned TaskForce</h6>
                                                    @if($staff->taskForces->isEmpty())
                                                        <p class="text-muted">No active TaskForce assigned.</p>
                                                    @else
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Task Force</th>
                                                                    <th>Role</th>
                                                                    <th>Weightage</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($staff->taskForces as $tf)
                                                                    <tr>
                                                                        <td>{{ $tf->name }}</td>
                                                                        <td>{{ $tf->pivot->role ?? 'Member' }}</td>
                                                                        <td>{{ $tf->default_weightage }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr class="table-light fw-bold">
                                                                    <td colspan="2" class="text-end">Total:</td>
                                                                    <td>{{ $staff->total_workload }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection