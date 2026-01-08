@extends('admin.layouts.app')

@section('title', 'Assign Departments - ' . $taskForce->name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Assign Departments to Task Force</h4>
            <p class="mb-0 text-muted">{{ $taskForce->name }}</p>
        </div>
        <div class="card-body">
            <!-- Current Assignment Info -->
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle"></i>
                <strong>Current Assignments:</strong>
                {{ $taskForce->departments->count() }} department(s) assigned
            </div>

            <form action="{{ route('admin.task-forces.assign-departments', $taskForce) }}" method="POST">
                @csrf

                <!-- Department Selection -->
                <div class="mb-4">
                    <label class="form-label">Select Departments <span class="text-danger">*</span></label>
                    <p class="text-muted small">Check all departments that should be part of this task force</p>

                    @if($allDepartments->count() > 0)
                        <div class="border rounded p-3">
                            <div class="row">
                                @foreach($allDepartments as $department)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="departments[]"
                                                value="{{ $department->id }}" id="dept_{{ $department->id }}"
                                                @if(in_array($department->id, $assignedDepartmentIds)) checked @endif>
                                            <label class="form-check-label" for="dept_{{ $department->id }}">
                                                <strong>{{ $department->name }}</strong>
                                                <span class="text-muted">({{ $department->code }})</span>
                                                @if($department->head)
                                                    <br>
                                                    <small class="text-muted">HOD: {{ $department->head->full_name }}</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @error('departments')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            No active departments found. Please create departments first.
                        </div>
                    @endif
                </div>

                <!-- Helper Buttons -->
                <div class="mb-4">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="bi bi-check-all"></i> Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                        <i class="bi bi-x"></i> Deselect All
                    </button>
                </div>

                <!-- Currently Assigned -->
                @if($taskForce->departments->count() > 0)
                    <div class="mb-4">
                        <label class="form-label">Currently Assigned Departments:</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($taskForce->departments as $dept)
                                <span class="badge bg-primary">
                                    {{ $dept->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-4">
                    @if($allDepartments->count() > 0)
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Assignment
                        </button>
                    @endif
                    <a href="{{ route('admin.task-forces.show', $taskForce) }}" class="btn btn-secondary">Cancel</a>
                    <a href="{{ route('admin.task-forces.index') }}" class="btn btn-outline-secondary">Back to List</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignment History (Optional) -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Assignment Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <h3 class="mb-0">{{ $allDepartments->count() }}</h3>
                        <small class="text-muted">Total Departments</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 bg-primary text-white rounded">
                        <h3 class="mb-0">{{ $taskForce->departments->count() }}</h3>
                        <small>Currently Assigned</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <h3 class="mb-0">{{ $allDepartments->count() - $taskForce->departments->count() }}</h3>
                        <small class="text-muted">Not Assigned</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function selectAll() {
            document.querySelectorAll('input[name="departments[]"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAll() {
            document.querySelectorAll('input[name="departments[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>
@endsection