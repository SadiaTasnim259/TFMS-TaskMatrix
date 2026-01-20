@extends('admin.layouts.app')

@section('title', 'Staff Management')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <p class="text-uppercase text-muted fw-semibold small mb-1">Admin • Staff</p>
            <h1 class="h3 section-title mb-0">Staff Management</h1>
            <p class="text-muted mb-0">Control account status, roles, and security resets for faculty members.</p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Create New Staff
            </a>
            <a href="{{ route('admin.users.import') }}" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-file-import me-2"></i> Import
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <!-- Search Box -->
                <div class="col-md-4">
                    <label class="form-label">Search Staff</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, Email..."
                        value="{{ request('search') }}">
                </div>

                <!-- Role Filter -->
                <div class="col-md-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @if(request('role') === $role->name) selected @endif>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" @if(request('status') === 'active') selected @endif>Active</option>
                        <option value="inactive" @if(request('status') === 'inactive') selected @endif>Inactive</option>
                        <option value="locked" @if(request('status') === 'locked') selected @endif>Locked</option>
                        <option value="must_change" @if(request('status') === 'must_change') selected @endif>Must Change
                            Password</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Staff ID</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <!-- ID -->
                            <td><strong>#{{ $user->id }}</strong></td>

                            <!-- Name -->
                            <td>
                                {{ $user->name }}
                                @if($user->is_first_login)
                                    <span class="badge bg-warning text-dark ms-1" title="Must change password">
                                        <i class="bi bi-key"></i>
                                    </span>
                                @endif
                            </td>

                            <!-- Email -->
                            <td>
                                <a href="mailto:{{ $user->email }}">
                                    {{ $user->email }}
                                </a>
                                @if($user->email_verified_at)
                                    <i class="bi bi-check-circle-fill text-success" title="Email verified"></i>
                                @endif
                            </td>

                            <!-- Role -->
                            <td>
                                <span class="badge bg-info">
                                    {{ $user->role->name ?? 'No Role' }}
                                </span>
                            </td>

                            <!-- Staff ID -->
                            <td>
                                @if($user->staff_id)
                                    <span class="badge bg-secondary">{{ $user->staff_id }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <!-- Status Badges -->
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif

                                    @if($user->locked_until && $user->locked_until > now())
                                        <span class="badge bg-danger">
                                            <i class="bi bi-lock"></i> Locked
                                        </span>
                                    @endif

                                    @if($user->failed_login_attempts > 0)
                                        <span class="badge bg-warning text-dark">
                                            {{ $user->failed_login_attempts }} fail(s)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Last Login -->
                            <td>
                                @if($user->last_login_at)
                                    <small>{{ $user->last_login_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border-0 rounded-circle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false"
                                        style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-ellipsis-v text-secondary"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                        <!-- View Details -->
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                                <i class="fas fa-eye me-2 text-muted"></i> View Details
                                            </a>
                                        </li>

                                        <!-- Edit User -->
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                                                <i class="fas fa-edit me-2 text-muted"></i> Edit Staff
                                            </a>
                                        </li>

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <!-- Unlock Button (if locked) -->
                                        @if($user->locked_until && $user->locked_until > now())
                                            <li>
                                                <form method="POST" action="{{ route('admin.users.unlock', $user) }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success">
                                                        <i class="fas fa-unlock me-2"></i> Unlock Account
                                                    </button>
                                                </form>
                                            </li>
                                        @endif

                                        <!-- Reset Password Button -->
                                        <li>
                                            <button type="button" class="dropdown-item text-warning" data-bs-toggle="modal"
                                                data-bs-target="#resetPasswordModal" data-user-name="{{ $user->name }}"
                                                data-reset-url="{{ route('admin.users.reset-password', $user) }}">
                                                <i class="fas fa-key me-2"></i> Reset Password
                                            </button>
                                        </li>

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <!-- Toggle Status -->
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="dropdown-item {{ $user->is_active ? 'text-danger' : 'text-success' }}">
                                                    @if($user->is_active)
                                                        <i class="fas fa-ban me-2"></i> Deactivate Staff
                                                    @else
                                                        <i class="fas fa-check-circle me-2"></i> Activate Staff
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            {{ $users->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ $users->total() }}</h3>
                    <small>Total Staff</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $users->where('is_active', true)->count() }}</h3>
                    <small>Active Staff</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3>{{ $users->where('locked_until', '>', now())->count() }}</h3>
                    <small>Locked Accounts</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h3>{{ $users->where('is_first_login', true)->count() }}</h3>
                    <small>Must Change Password</small>
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
                    <p>Are you sure you want to reset the password for <strong id="modalUserName"></strong>?</p>
                    <p class="text-muted small mb-0">This will generate a new temporary password and email it to the user.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="resetPasswordForm" method="POST" action="">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var resetPasswordModal = document.getElementById('resetPasswordModal');
            resetPasswordModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var userName = button.getAttribute('data-user-name');
                var resetUrl = button.getAttribute('data-reset-url');

                var modalUserName = resetPasswordModal.querySelector('#modalUserName');
                var resetPasswordForm = resetPasswordModal.querySelector('#resetPasswordForm');

                modalUserName.textContent = userName;
                resetPasswordForm.action = resetUrl;
            });
        });
    </script>
@endsection