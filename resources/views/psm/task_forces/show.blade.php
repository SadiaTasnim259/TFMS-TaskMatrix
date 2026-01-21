@extends('admin.layouts.app')

@section('title', 'Task Force Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $taskForce->name }}</h1>
        </div>
        <div class="d-flex gap-2">
            @if ($taskForce->is_locked)
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#unlockModal">
                    <i class="fas fa-unlock me-1"></i> Unlock
                </button>
            @else
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#lockModal">
                    <i class="fas fa-lock me-1"></i> Lock
                </button>
            @endif
            <a href="{{ route('psm.task-forces.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row g-4 mb-4">
        <!-- Metadata -->
        <div class="col-md-12">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Task Force Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted fw-bold">Department(s)</div>
                        <div class="col-sm-9">
                            @if($taskForce->departments->count() > 0)
                                {{ $taskForce->departments->pluck('name')->join(', ') }}
                            @else
                                <span class="fst-italic text-muted">Global / Unassigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted fw-bold">Description</div>
                        <div class="col-sm-9">{{ $taskForce->description }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted fw-bold">Status</div>
                        <div class="col-sm-9">
                            @if ($taskForce->is_locked)
                                <span class="badge bg-secondary">Locked</span>
                                <small class="text-muted ms-2">Modifications disabled. Unlock to edit.</small>
                            @else
                                <span class="badge bg-danger">Unlocked</span>
                                <small class="text-muted ms-2">Currently being modified.</small>
                                @if($taskForce->justification)
                                    <div class="alert alert-info py-2 px-3 mt-2 mb-0 small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Justification:</strong> {{ $taskForce->justification }}
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>


    <!-- Pending Requests -->
    @if($taskForce->membershipRequests->where('status', 'pending')->count() > 0)
        <div class="card shadow mb-4 border-start-warning" id="pending-requests">
            <div class="card-header py-3 bg-warning bg-opacity-10 border-bottom-0">
                <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-clock me-1"></i> Pending Membership Requests</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Request Type</th>
                                <th>Staff Member</th>
                                <th>Role</th>
                                <th>Requested By</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($taskForce->membershipRequests->where('status', 'pending') as $req)
                                <tr>
                                    <td class="ps-4">
                                        @if($req->action === 'add')
                                            <span class="badge bg-success bg-opacity-10 text-success">Add Member</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger">Remove Member</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $req->user->name }}</div>
                                        <div class="small text-muted">{{ $req->user->email }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ ucfirst($req->role) }}</span>
                                    </td>
                                    <td>
                                        <div class="small">{{ $req->requester->name }}</div>
                                        <div class="small text-muted">{{ $req->requester->department->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('psm.task-forces.approve-request', [$taskForce->id, $req->id]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success me-1" title="Approve & Lock">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectRequestModal"
                                            data-request-url="{{ route('psm.task-forces.reject-request', [$taskForce->id, $req->id]) }}"
                                            data-requester-name="{{ $req->requester->name }}" title="Reject">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Members List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <h6 class="m-0 font-weight-bold text-primary">Assigned Members</h6>
                <span class="badge bg-info text-dark">{{ $taskForce->members->count() }} Members</span>
            </div>
            {{-- Hidden as per request
            @if (!$taskForce->is_locked)
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="fas fa-plus me-1"></i> Add Member
            </button>
            @endif
            --}}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Staff Member</th>
                            <th>Role</th>
                            <th class="text-center">Current Workload</th>
                            @if (!$taskForce->is_locked)
                                <th class="text-end pe-4">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($taskForce->members as $member)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2 bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $member->name }}</div>
                                            <div class="small text-muted">{{ $member->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ucfirst($member->pivot->role) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold {{ $member->total_workload > 20 ? 'text-danger' : 'text-success' }}">
                                        {{ $member->total_workload }}
                                    </span>
                                </td>
                                @if (!$taskForce->is_locked)
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-light text-danger" data-bs-toggle="modal"
                                            data-bs-target="#removeMemberModal" data-member-name="{{ $member->name }}"
                                            data-remove-url="{{ route('psm.task-forces.remove-member', [$taskForce->id, $member->id]) }}"
                                            title="Remove Member">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <td colspan="{{ $taskForce->is_locked ? 3 : 4 }}" class="text-center py-4 text-muted">No members
                                assigned yet.</td>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Unlock Modal -->
    <div class="modal fade" id="unlockModal" tabindex="-1" aria-labelledby="unlockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('psm.task-forces.unlock', $taskForce->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="unlockModalLabel">Unlock Task Force</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Unlocking allows modification. Logs will be recorded.
                        </div>
                        <div class="mb-3">
                            <label for="justification" class="form-label">Justification <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="justification" name="justification" rows="3" required
                                placeholder="Why is modification needed?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Unlock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lock Modal -->
    <div class="modal fade" id="lockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('psm.task-forces.lock', $taskForce->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Lock Task Force</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you done with all modifications?</p>
                        <p class="mb-0">Locking will prevent further changes until unlocked again.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Lock Task Force</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Member to Task Force</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('psm.task-forces.add-member', $taskForce->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select Staff</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">-- Choose Staff Member --</option>
                                @foreach($availableStaff as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="Member">Member</option>
                                <option value="Chair">Chair</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Coordinator">Coordinator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Member Modal -->
    <div class="modal fade" id="removeMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Remove Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove <strong id="modalMemberName"></strong>?</p>
                    <p class="text-danger small mb-0"><i class="fas fa-exclamation-circle me-1"></i> An email notification
                        will be sent.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="removeMemberForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirm Removal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Remove Member Modal
            var removeMemberModal = document.getElementById('removeMemberModal');
            if (removeMemberModal) {
                removeMemberModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var memberName = button.getAttribute('data-member-name');
                    var removeUrl = button.getAttribute('data-remove-url');

                    var modalMemberName = removeMemberModal.querySelector('#modalMemberName');
                    var removeForm = removeMemberModal.querySelector('#removeMemberForm');

                    modalMemberName.textContent = memberName;
                    removeForm.action = removeUrl;
                });
            }

            // Reject Request Modal
            var rejectRequestModal = document.getElementById('rejectRequestModal');
            if (rejectRequestModal) {
                rejectRequestModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var requestUrl = button.getAttribute('data-request-url');
                    var requesterName = button.getAttribute('data-requester-name');

                    var modalRequesterName = rejectRequestModal.querySelector('#modalRequesterName');
                    var rejectForm = rejectRequestModal.querySelector('#rejectRequestForm');

                    modalRequesterName.textContent = requesterName;
                    rejectForm.action = requestUrl;
                });
            }
        });
    </script>

    <!-- Reject Request Modal -->
    <div class="modal fade" id="rejectRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Membership Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="rejectRequestForm" method="POST" action="">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to reject the request from <strong id="modalRequesterName"></strong>?</p>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Reason for Rejection <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" required
                                placeholder="Please provide a reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection