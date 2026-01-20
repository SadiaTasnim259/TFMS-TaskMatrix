@extends('admin.layouts.app')

@section('title', 'Audit Logs')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <p class="text-uppercase text-muted fw-semibold small mb-1">Admin • Audit</p>
            <h1 class="h3 section-title mb-0">Audit Logs</h1>
            <p class="text-muted mb-0">Trace every change with filters for action, model, user, and date.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.audit-logs.export-csv') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
            <a href="{{ route('admin.audit-logs.export-excel') }}" class="btn btn-outline-secondary">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-3">
                <!-- Search Box -->
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Model name, description..."
                        value="{{ request('search') }}">
                </div>

                <!-- Action Type Filter -->
                <div class="col-md-2">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" @if(request('action') === $action) selected @endif>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <!-- User Filter -->
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @if(request('user_id') == $user->id) selected @endif>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>IP Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <!-- Timestamp -->
                            <td>
                                <div class="fw-semibold">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </td>

                            <!-- User -->
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-2"
                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                            {{ substr($log->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $log->user->name }}</div>
                                            <small class="text-muted">{{ $log->user->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">System</span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td>
                                @php
                                    $actionClass = match ($log->action) {
                                        'CREATE' => 'success',
                                        'UPDATE' => 'warning',
                                        'DELETE' => 'danger',
                                        'LOGIN' => 'info',
                                        'LOGOUT' => 'secondary',
                                        default => 'primary'
                                    };
                                @endphp
                                <span
                                    class="badge bg-{{ $actionClass }} bg-opacity-10 text-{{ $actionClass }} border border-{{ $actionClass }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <!-- Model -->
                            <td>
                                <div class="fw-semibold">{{ $log->model_type }}</div>
                                @if($log->model_name)
                                    <small class="text-muted">{{ Str::limit($log->model_name, 30) }}</small>
                                @endif
                            </td>

                            <!-- Description -->
                            <td>
                                <small class="text-muted">{{ Str::limit($log->description, 50) }}</small>
                            </td>

                            <!-- Status -->
                            <td>
                                @if($log->status === 'completed' || $log->status === 'SUCCESS')
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="fas fa-check-circle me-1"></i> Success
                                    </span>
                                @elseif($log->status === 'failed' || $log->status === 'FAILED')
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="fas fa-times-circle me-1"></i> Failed
                                    </span>
                                @elseif($log->status === 'pending')
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- IP Address -->
                            <td>
                                <small class="font-monospace text-muted">{{ $log->ip_address ?? 'N/A' }}</small>
                            </td>

                            <!-- Actions -->
                            <td class="text-end">
                                <a href="{{ route('admin.audit-logs.show', $log) }}" class="btn btn-light btn-sm text-primary"
                                    title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <div class="mb-3">
                                    <i class="fas fa-clipboard-list fa-3x text-muted opacity-25"></i>
                                </div>
                                <p class="mb-0">No audit logs found matching your criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer bg-white border-top-0">
            {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4 g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-primary mb-1">{{ $logs->total() }}</div>
                    <div class="text-muted small text-uppercase tracking-wide">Total Logs</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-success mb-1">{{ $actionCounts['CREATE'] ?? 0 }}</div>
                    <div class="text-muted small text-uppercase tracking-wide">Create Actions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-warning mb-1">{{ $actionCounts['UPDATE'] ?? 0 }}</div>
                    <div class="text-muted small text-uppercase tracking-wide">Update Actions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-danger mb-1">{{ $logs->where('action', 'DELETE')->count() }}</div>
                    <div class="text-muted small text-uppercase tracking-wide">Delete Actions</div>
                </div>
            </div>
        </div>
    </div>
@endsection