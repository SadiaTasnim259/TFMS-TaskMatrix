@extends('admin.layouts.app')

@section('content')

<div class="container-fluid"> <!-- Header --> <div class="row mb-4"> <div class="col-md-8"> <h1 class="h3 mb-0">{{ $taskForce->name }}</h1> <p class="text-muted">Task Force Details</p> </div> <div class="col-md-4 text-end"> <a href="{{ route('admin.task-forces.edit', $taskForce) }}" class="btn btn-warning"> <i class="fas fa-edit"></i> Edit </a> <a href="{{ route('admin.task-forces.index') }}" class="btn btn-secondary"> <i class="fas fa-arrow-left"></i> Back </a> </div> </div>
<!-- Alerts -->


<!-- Main Details -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Task Force Name</h6>
                        <p class="mb-0"><strong>{{ $taskForce->name }}</strong></p>
                    </div>

                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Status</h6>
                        <p class="mb-0">
                            @if ($taskForce->active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Weightage</h6>
                        <p class="mb-0">
                            <span class="badge bg-warning">{{ $taskForce->default_weightage ?? '0' }}</span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Description</h6>
                        <p class="mb-0">
                            @if ($taskForce->description)
                                {{ $taskForce->description }}
                            @else
                                <span class="text-muted">No description provided</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row">

                    <div class="col-md-6">
                        <h6 class="text-muted">Number of Departments</h6>
                        <p class="mb-0">
                            <strong>{{ $taskForce->departments->count() }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Status Card -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <!-- Status Badge -->
                <div class="mb-3 text-center">
                    @if($taskForce->isLocked())
                        <div class="alert alert-warning mb-2">
                            <i class="fas fa-lock"></i> <strong>LOCKED</strong>
                        </div>
                        <small class="text-muted d-block mb-2">Locked for HOD modifications.</small>
                        
                        <!-- Unlock Action (Exceptional Modification) -->
                        <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#unlockModal">
                            <i class="fas fa-key"></i> Unlock Task Force
                        </button>
                    @else
                        <div class="alert alert-success mb-2">
                            <i class="fas fa-lock-open"></i> <strong>UNLOCKED</strong>
                        </div>
                        @if($taskForce->justification)
                            <div class="alert alert-info py-2 small mb-2 text-start">
                                <strong><i class="fas fa-info-circle"></i> Reason:</strong><br>
                                {{ $taskForce->justification }}
                            </div>
                        @endif
                    @endif
                </div>

                <form action="{{ route('admin.task-forces.toggle-status', $taskForce) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-sm w-100 @if($taskForce->active) btn-warning @else btn-success @endif">
                        <i class="fas fa-toggle-@if($taskForce->active)on @else off @endif"></i>
                        {{ $taskForce->active ? 'Deactivate' : 'Activate' }} Task Force
                    </button>
                </form>
                <a href="{{ route('admin.task-forces.assign-departments.form', $taskForce) }}" class="btn btn-sm btn-info w-100">
                    <i class="fas fa-link"></i> Assign Departments
                </a>
            </div>
        </div>

        <!-- Unlock Modal -->
        <div class="modal fade" id="unlockModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('psm.task-forces.unlock', $taskForce->id) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">Unlock Task Force (Exceptional Modification)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Unlocking this task force will allow HODs to modify membership again.</p>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Justification (Required)</label>
                                <textarea name="justification" class="form-control" rows="3" required placeholder="Reason for unlocking..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">Confirm Unlock</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Timestamps Card -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Timestamps</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <p class="mb-2">
                        <strong>Created:</strong><br>
                        {{ $taskForce->created_at->format('M d, Y \a\t H:i') }}
                    </p>
                    <p class="mb-0">
                        <strong>Last Updated:</strong><br>
                        {{ $taskForce->updated_at->format('M d, Y \a\t H:i') }}
                    </p>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Departments -->
<div class="card mb-4">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <h5 class="mb-0">Assigned Departments</h5>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.task-forces.assign-departments.form', $taskForce) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-plus"></i> Manage Assignments
                </a>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        @if ($taskForce->departments->count() > 0)
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Department Code</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($taskForce->departments as $dept)
                        <tr>
                            <td><strong>{{ $dept->name }}</strong></td>
                            <td><code>{{ $dept->code }}</code></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-4 text-center text-muted">
                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <p>No departments assigned to this task force yet.</p>
                <a href="{{ route('admin.task-forces.assign-departments.form', $taskForce) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Assign First Department
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Danger Zone -->
<div class="card border-danger">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Danger Zone</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">Deleting this task force is permanent and cannot be undone.</p>
        <button 
            type="button" 
            class="btn btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteModal"
        >
            <i class="fas fa-trash"></i> Delete Task Force
        </button>
    </div>
</div>
</div> <!-- Delete Confirmation Modal --> <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog"> <div class="modal-dialog" role="document"> <div class="modal-content"> <div class="modal-header border-danger"> <h5 class="modal-title text-danger">Delete Task Force</h5> <button type="button" class="btn-close" data-bs-dismiss="modal"></button> </div> <div class="modal-body"> <p>Are you sure you want to permanently delete this task force?</p> <p><strong>{{ $taskForce->name }}</strong></p> <p class="text-danger"><strong>This action cannot be undone.</strong></p> </div> <div class="modal-footer"> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> <form action="{{ route('admin.task-forces.destroy', $taskForce) }}" method="POST" style="display: inline;"> @csrf @method('DELETE') <button type="submit" class="btn btn-danger">Delete Task Force</button> </form> </div> </div> </div> </div> <style> .card { border: 1px solid #e3e6f0; border-radius: 0.35rem; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); } .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; } .card-header.bg-light { background-color: #f8f9fc !important; } .badge { font-size: 0.75rem; padding: 0.35rem 0.65rem; } .table th { border-top: none; font-weight: 600; background-color: #f8f9fc; color: #495057; border-bottom: 2px solid #e3e6f0; } </style>
@endsection