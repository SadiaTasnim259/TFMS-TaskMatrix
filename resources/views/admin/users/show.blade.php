@extends('admin.layouts.app')

@section('title', 'Staff Details')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <p class="text-uppercase text-muted fw-semibold small mb-1">Admin • Staff</p>
            <h1 class="h3 section-title mb-0">{{ $user->name }}</h1>
        </div>
        <div>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">
                Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->role->name }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        @if($user->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                        
                        @if($user->isLocked())
                            <span class="badge bg-danger">Locked</span>
                        @endif
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between text-muted">
                        Email: <span>{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between text-muted">
                        Joined: <span>{{ $user->created_at->format('M d, Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between text-muted">
                        Last Login: <span>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Staff Data & Misc -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Staff Details</h5>
                </div>
                <div class="card-body">
                    @if($user->staff_id)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Staff ID</label>
                                <p class="fw-bold">{{ $user->staff_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Department</label>
                                <p class="fw-bold">{{ $user->department->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Grade</label>
                                <p class="fw-bold">{{ $user->grade ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Employment Status</label>
                                <p class="fw-bold">{{ $user->employment_status ?? 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small">Additional Roles</label>
                                <div>
                                    @if($user->is_hod) <span class="badge bg-info">HOD</span> @endif
                                    @if($user->is_task_force_chair) <span class="badge bg-info">Task Force Chair</span> @endif
                                    @if($user->is_program_coordinator) <span class="badge bg-info">Program Coordinator</span> @endif
                                    @if(!$user->is_hod && !$user->is_task_force_chair && ! $user->is_program_coordinator)
                                        <span class="text-muted fst-italic">None</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small">Notes</label>
                                <p class="text-muted">{{ $user->notes ?? 'No notes available.' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-person-badge display-4 mb-3 d-block"></i>
                            This user is not linked to any staff profile.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity / Permissions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Account Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                            @csrf
                            <button class="btn btn-outline-warning">
                                {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-outline-danger" 
                            data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                            Reset Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reset the password for <strong>{{ $user->name }}</strong>?</p>
                    <p class="text-muted small mb-0">This will generate a new temporary password and email it to the user.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                        @csrf
                        <input type="hidden" name="confirm" value="1">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i> Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
