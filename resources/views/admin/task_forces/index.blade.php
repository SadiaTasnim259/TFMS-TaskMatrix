@extends('admin.layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <p class="text-uppercase text-muted fw-semibold small mb-1">Admin • Task Forces</p>
            <h1 class="h3 section-title mb-0">Task Force Registry</h1>
            <p class="text-muted mb-0">Manage task forces, ownership, and departmental assignments.</p>
        </div>
        <a href="{{ route('admin.task-forces.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus"></i> Create Task Force
        </a>
    </div>



    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.task-forces.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Task force name..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">-- All Status --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive" style="min-height: 400px;">
            @if ($taskForces->count() > 0)
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Task Force</th>


                            <th>Departments</th>
                            <th>Weightage</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th style="width: 180px;" class="text-end">Actions</th>
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
                                    <span
                                        class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ $taskForce->departments->count() }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-warning bg-opacity-10 text-warning rounded-pill">{{ $taskForce->default_weightage ?? '0' }}</span>
                                </td>
                                <td>
                                    @if ($taskForce->active)
                                        <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $taskForce->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border-0 rounded-circle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false"
                                            style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-ellipsis-v text-secondary"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                            <li>
                                                <a href="{{ route('admin.task-forces.show', $taskForce) }}" class="dropdown-item">
                                                    <i class="fas fa-eye me-2 text-primary"></i> View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.task-forces.edit', $taskForce) }}" class="dropdown-item">
                                                    <i class="fas fa-edit me-2 text-warning"></i> Edit Task Force
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.task-forces.assign-departments.form', $taskForce) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-link me-2 text-info"></i> Assign Departments
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.task-forces.toggle-status', $taskForce) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="dropdown-item {{ $taskForce->active ? 'text-danger' : 'text-success' }}">
                                                        @if($taskForce->active)
                                                            <i class="fas fa-ban me-2"></i> Deactivate
                                                        @else
                                                            <i class="fas fa-check-circle me-2"></i> Activate
                                                        @endif
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    onclick="setDeleteForm('{{ route('admin.task-forces.destroy', $taskForce) }}')">
                                                    <i class="fas fa-trash me-2"></i> Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>No task forces found.</p>
                    <a href="{{ route('admin.task-forces.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Create First Task Force
                    </a>
                </div>
            @endif
        </div>

        @if ($taskForces->count() > 0)
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing {{ $taskForces->firstItem() ?? 0 }} to {{ $taskForces->lastItem() ?? 0 }}
                    of {{ $taskForces->total() }} task forces
                </small>
                {{ $taskForces->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Task Force</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this task force?</p>
                    <p class="text-danger"><small><strong>This action cannot be undone.</strong></small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Task Force</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setDeleteForm(action) {
            document.getElementById('deleteForm').action = action;
        }
    </script>
@endsection