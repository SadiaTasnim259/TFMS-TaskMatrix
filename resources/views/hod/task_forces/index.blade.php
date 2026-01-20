@extends('hod.layouts.app')

@section('title', 'My Department Task Forces')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Department Task Forces</h1>
            <p class="text-muted mb-0">
                View and manage task forces assigned to your department
                @if(isset($currentSession))
                    <span class="badge bg-primary ms-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $currentSession->academic_year }} - Semester {{ $currentSession->semester }}
                    </span>
                @endif
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('hod.task-forces.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Task force name..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-4">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">-- All Categories --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Forces Table -->
    <div class="card">
        <div class="table-responsive" style="min-height: 400px;">
            @if ($taskForces->count() > 0)
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Task Force Name</th>
                            <th>Assignment Status</th>
                            <th>Session</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taskForces as $taskForce)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $taskForce->name }}</div>
                                    @if ($taskForce->description)
                                        <small class="text-muted">{{ Str::limit($taskForce->description, 60) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $hasPending = $taskForce->membershipRequests->where('status', 'pending')->isNotEmpty();
                                        $hasRejected = $taskForce->membershipRequests->where('status', 'rejected')->isNotEmpty();
                                        $isLocked = $taskForce->isLocked();
                                        $memberCount = $taskForce->members->count();
                                    @endphp

                                    @if($isLocked)
                                        <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i> Locked</span>
                                    @elseif($hasPending)
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($hasRejected)
                                        <span class="badge bg-danger">Rejected</span>
                                    @elseif($memberCount == 0)
                                        <span class="badge bg-danger">Not Assigned</span>
                                    @else
                                        <span class="badge bg-success">Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $taskForce->academic_year }}</span>
                                </td>
                                <td>
                                    @if($taskForce->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $taskForce->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('hod.task-forces.show', $taskForce->id) }}">
                                                    <i class="fas fa-eye me-2"></i> View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('hod.task-forces.show', $taskForce->id) }}">
                                                    <i class="fas fa-users me-2"></i> Manage Members
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                    <p>No task forces found for your department matching the criteria.</p>
                </div>
            @endif
        </div>

        @if($taskForces->hasPages())
            <div class="card-footer">
                {{ $taskForces->links() }}
            </div>
        @endif
    </div>

@endsection