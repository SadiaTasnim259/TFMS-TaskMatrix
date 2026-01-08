@extends('admin.layouts.app')

@section('title', 'Edit Task Force')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Edit Task Force: {{ $taskForce->name }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.task-forces.update', $taskForce) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Task Force ID (Read-only) -->
                    <div class="col-md-6 mb-3">
                        <label for="task_force_id" class="form-label">Task Force ID</label>
                        <input type="text" class="form-control" id="task_force_id" value="{{ $taskForce->task_force_id }}"
                            disabled>
                        <small class="form-text text-muted">Task Force ID cannot be changed</small>
                    </div>

                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Task Force Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                            value="{{ old('name', $taskForce->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">


                    <!-- Default Weightage -->
                    <div class="col-md-6 mb-3">
                        <label for="default_weightage" class="form-label">Default Weightage <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="1.0" max="{{ $maxWorkload }}"
                            class="form-control @error('default_weightage') is-invalid @enderror" id="default_weightage"
                            name="default_weightage" value="{{ old('default_weightage', $taskForce->default_weightage) }}"
                            required>
                        <small class="form-text text-muted">Value between 1.0 and {{ $maxWorkload }}</small>
                        @error('default_weightage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                        name="description" rows="4">{{ old('description', $taskForce->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <!-- Owner/Chair -->


                    <!-- Active Status -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                                @if(old('active', $taskForce->active)) checked @endif>
                            <label class="form-check-label" for="active">
                                Active (Task force is currently operational)
                            </label>
                        </div>
                    </div>
                </div>



                <!-- Assigned Departments (read-only info) -->
                <div class="mb-3">
                    <label class="form-label">Currently Assigned Departments</label>
                    <div class="border rounded p-3 bg-light">
                        @if($taskForce->departments->count() > 0)
                            @foreach($taskForce->departments as $dept)
                                <span class="badge bg-primary me-1">{{ $dept->name }}</span>
                            @endforeach
                            <div class="mt-2">
                                <a href="{{ route('admin.task-forces.assign-departments.form', $taskForce) }}"
                                    class="btn btn-sm btn-info">
                                    <i class="bi bi-pencil"></i> Manage Department Assignments
                                </a>
                            </div>
                        @else
                            <p class="text-muted mb-0">No departments assigned yet.</p>
                            <a href="{{ route('admin.task-forces.assign-departments.form', $taskForce) }}"
                                class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus"></i> Assign Departments
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Task Force
                    </button>
                    <a href="{{ route('admin.task-forces.show', $taskForce) }}" class="btn btn-secondary">Cancel</a>
                    <a href="{{ route('admin.task-forces.index') }}" class="btn btn-outline-secondary">Back to List</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card mt-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Danger Zone</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Deleting a task force is permanent and cannot be undone. All assignments will be
                removed.</p>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTaskForceModal">
                <i class="bi bi-trash"></i> Delete Task Force
            </button>
        </div>
    </div>

    <!-- Delete Task Force Modal -->
    <div class="modal fade" id="deleteTaskForceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Task Force</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the task force <strong>{{ $taskForce->name }}</strong>?</p>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        This action is permanent and cannot be undone. All member assignments will be removed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.task-forces.destroy', $taskForce) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i> Yes, Delete Task Force
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection