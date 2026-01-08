@extends('hod.layouts.app')

@section('title', 'Task Force Details')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">{{ $taskForce->name }}</h1>
                <div class="mt-2">
                    <span class="badge bg-info text-dark me-1">{{ $taskForce->category }}</span>
                    <span class="badge bg-secondary">{{ $taskForce->academic_year }}</span>
                </div>
            </div>
            <a href="{{ route('hod.task-forces.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="row">
            <!-- Left Column: Settings & Metadata -->
            <div class="col-lg-4 mb-4">
                <!-- About Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white border-bottom-0">
                        <h6 class="m-0 font-weight-bold text-primary">About Task Force</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="small text-uppercase text-muted font-weight-bold">Status</label>
                            <div>
                                @if($taskForce->active)
                                    <span class="badge bg-success rounded-pill px-3">Active</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3">Inactive</span>
                                @endif
                                @if($taskForce->isLocked())
                                    <span class="badge bg-warning text-dark rounded-pill px-3 ms-1"><i
                                            class="fas fa-lock me-1"></i> Locked</span>
                                @endif

                                {{-- Justification Alert --}}
                                @if(!$taskForce->isLocked() && $taskForce->justification)
                                    <div class="alert alert-info py-2 px-3 mt-3 mb-0 small border-start-info">
                                        <div class="fw-bold"><i class="fas fa-info-circle me-1"></i> Unlocked Reason:</div>
                                        <div class="mt-1">{{ $taskForce->justification }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="small text-uppercase text-muted font-weight-bold">Default Weightage</label>
                            <div class="h4 mb-0 font-weight-bold text-dark">{{ $taskForce->default_weightage }}</div>
                        </div>

                        <div class="mb-0">
                            <label class="small text-uppercase text-muted font-weight-bold">Description</label>
                            <p class="mb-0 text-muted">
                                {{ $taskForce->description ?? 'No description provided.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Draft Requests (NEW SECTION) -->
                @if(isset($draftRequests) && $draftRequests->count() > 0)
                    <div class="card shadow mb-4 border-start-info">
                        <div
                            class="card-header py-3 bg-info bg-opacity-10 border-bottom-0 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-edit me-1"></i> Draft Members</h6>
                            <span class="badge bg-info text-dark rounded-pill">{{ $draftRequests->count() }}</span>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($draftRequests as $draft)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $draft->user->full_name }}</div>
                                        <small class="text-muted">{{ $draft->role }}</small>
                                    </div>
                                    <form action="{{ route('hod.task-forces.delete-draft', [$taskForce->id, $draft->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Remove Draft">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                            <form action="{{ route('hod.task-forces.submit-requests', $taskForce->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info text-white w-100 fw-bold shadow-sm">
                                    <i class="fas fa-paper-plane me-1"></i> Submit to PSM
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Pending Requests -->
                @if(isset($pendingRequests) && $pendingRequests->count() > 0)
                    <div class="card shadow mb-4 border-start-warning">
                        <div class="card-header py-3 bg-warning bg-opacity-10 border-bottom-0">
                            <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-clock me-1"></i> Pending Requests
                            </h6>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($pendingRequests as $req)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold {{ $req->action === 'add' ? 'text-success' : 'text-danger' }}">
                                            {{ $req->action === 'add' ? 'Add' : 'Remove' }}
                                        </span>
                                        <div>
                                            <small class="text-muted me-2">{{ $req->created_at->diffForHumans() }}</small>

                                            <!-- Actions: Edit (Add only) / Cancel -->
                                            @if($req->action === 'add')
                                                <button class="btn btn-sm btn-link text-primary p-0 me-2" data-bs-toggle="modal"
                                                    data-bs-target="#editRequestModal" data-request-id="{{ $req->id }}"
                                                    data-current-role="{{ $req->role }}"
                                                    data-update-url="{{ route('hod.task-forces.update-request', [$taskForce->id, $req->id]) }}"
                                                    title="Edit Role">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            @endif

                                            <form action="{{ route('hod.task-forces.cancel-request', [$taskForce->id, $req->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0 cool-confirm"
                                                    title="Withdraw Request">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-dark">{{ $req->user->full_name }}</div>
                                    @if($req->role)
                                        <small class="text-muted">Role: {{ $req->role }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Members -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div
                        class="card-header py-3 bg-white d-flex align-items-center justify-content-between border-bottom-0">
                        <h6 class="m-0 font-weight-bold text-primary">Assigned Members</h6>
                        @if($taskForce->isLocked())
                            <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" disabled>
                                <i class="fas fa-lock me-1"></i> Add Member
                            </button>
                        @else
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal"
                                data-bs-target="#addMemberModal">
                                <i class="fas fa-plus me-1"></i> Add Member
                            </button>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            @if($taskForce->members->count() > 0)
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3">Staff Member</th>
                                            <th class="py-3">Role</th>
                                            <th class="py-3">Assigned By</th>
                                            <th class="py-3">Joined</th>
                                            <th class="pe-4 py-3 text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($taskForce->members as $member)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar me-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 35px; height: 35px; font-weight: 600;">
                                                            {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-weight-bold text-dark">{{ $member->first_name }}
                                                                {{ $member->last_name }}
                                                            </div>
                                                            <div class="small text-muted">{{ $member->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span
                                                        class="badge bg-light text-dark border fw-normal">{{ $member->pivot->role }}</span>
                                                </td>
                                                <td class="small text-muted">
                                                    @php $assigner = \App\Models\User::find($member->pivot->assigned_by); @endphp
                                                    {{ $assigner ? $assigner->first_name : 'System' }}
                                                </td>
                                                <td class="small text-muted">
                                                    {{ \Carbon\Carbon::parse($member->pivot->created_at)->format('d M Y') }}
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <button class="btn btn-sm btn-link text-danger p-0" data-bs-toggle="modal"
                                                        data-bs-target="#removeMemberModal"
                                                        data-member-name="{{ $member->first_name }} {{ $member->last_name }}"
                                                        data-remove-url="{{ route('hod.task-forces.remove-member', [$taskForce->id, $member->id]) }}"
                                                        title="Remove Member">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-users-slash fa-3x text-muted opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted">No members assigned yet</h6>
                                    <p class="small text-muted mb-0">Click "Add Member" to get started.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Member to Task Force</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('hod.task-forces.add-member', $taskForce->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="staff_id" class="form-label fw-bold small text-uppercase text-muted">Select
                                Staff</label>
                            <select class="form-select" id="staff_id" name="staff_id" required>
                                <option value="">-- Choose Staff Member --</option>
                                @foreach($availableStaff as $staff)
                                    <option value="{{ $staff->id }}">
                                        {{ $staff->first_name }} {{ $staff->last_name }}
                                        &bull; {{ $staff->employmentStatusLabel() }}
                                        &bull; {{ $staff->current_load }}% Load
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small"><i class="fas fa-info-circle me-1"></i> Staff are sorted by lowest
                                current workload.</div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold small text-uppercase text-muted">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="Member">Member</option>
                                <option value="Chair">Chair</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Coordinator">Coordinator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-link text-muted no-underline"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of Add Member Modal -->

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
                    <p>Are you sure you want to remove <strong id="modalMemberName"></strong> from this task force?</p>
                    <div class="text-danger small">
                        <i class="fas fa-exclamation-circle me-1"></i> This removes their workload allocation for this task
                        force.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="removeMemberForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i> Remove Member
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Request Modal -->
    <div class="modal fade" id="editRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Update Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editRequestForm" action="" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_role" class="form-label fw-bold small text-uppercase text-muted">New
                                Role</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="Member">Member</option>
                                <option value="Chair">Chair</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Coordinator">Coordinator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-link text-muted no-underline"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Remove Member Modal logic
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

            // Edit Request Modal logic
            var editRequestModal = document.getElementById('editRequestModal');
            if (editRequestModal) {
                editRequestModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var currentRole = button.getAttribute('data-current-role');
                    var updateUrl = button.getAttribute('data-update-url');

                    var editRoleSelect = editRequestModal.querySelector('#edit_role');
                    var editForm = editRequestModal.querySelector('#editRequestForm');

                    if (currentRole) {
                        editRoleSelect.value = currentRole;
                    }
                    editForm.action = updateUrl;
                });
            }
        });
    </script>
@endsection