@extends('admin.layouts.app')

@section('title', 'Faculty TaskForce')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Faculty TaskForce Directory</h1>
            <p class="text-muted mb-0">
                View and manage all TaskForce across departments.
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
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('psm.task-forces.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label small text-muted text-uppercase fw-bold">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Search by name or description...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="department_id" class="form-label small text-muted text-uppercase fw-bold">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('psm.task-forces.index') }}" class="btn btn-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Force List -->
    <div class="card shadow mb-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Task Force Name</th>
                            <th class="py-3">Department</th>
                            <th class="py-3">Details</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($taskForces as $tf)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $tf->name }}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 250px;">
                                        {{ Str::limit($tf->description, 50) }}
                                    </div>
                                </td>
                                <td>
                                    @if ($tf->departments->count() > 0)
                                        @foreach($tf->departments as $dept)
                                            <span class="badge bg-light text-dark border me-1">{{ $dept->code }}</span>
                                        @endforeach
                                        <div class="small text-muted mt-1 d-none d-lg-block">
                                            {{ $tf->departments->pluck('name')->join(', ') }}
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Global / Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="fas fa-users me-1 text-muted"></i> {{ $tf->members_count }} Members
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($tf->is_locked)
                                        <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i> Locked</span>
                                    @else
                                        <span class="badge bg-success"><i class="fas fa-lock-open me-1"></i> Open</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('psm.task-forces.show', $tf->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i>
                                    <p class="mb-0">No TaskForce found matching your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end">
            {{ $taskForces->withQueryString()->links() }}
        </div>
    </div>
@endsection