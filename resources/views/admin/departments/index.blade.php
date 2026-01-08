@extends('admin.layouts.app')

@section('title', 'Departments')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <p class="text-uppercase text-muted fw-semibold small mb-1">Admin • Departments</p>
            <h1 class="h3 section-title mb-0">Department Directory</h1>
            <p class="text-muted mb-0">Maintain department records and keep ownership data clean.</p>
        </div>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-warning text-utm-maroon fw-semibold shadow-sm">
            <i class="fas fa-plus"></i> Add Department
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Staff</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr>
                            <td class="fw-bold">{{ $department->code }}</td>
                            <td>{{ $department->name }}</td>
                            <td class="text-muted">{{ Str::limit($department->description, 60) }}</td>
                            <td>
                                <span class="badge bg-info rounded-pill">{{ $department->staff_count }}</span>
                            </td>
                            <td>
                                @if($department->active)
                                    <span class="badge bg-success rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-danger rounded-pill">Inactive</span>
                                @endif
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
                                            <a href="{{ route('admin.departments.edit', $department) }}" class="dropdown-item">
                                                <i class="fas fa-edit me-2 text-warning"></i> Edit
                                            </a>
                                        </li>
                                        @if($department->staff_count == 0)
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteDepartmentModal" data-dept-name="{{ $department->name }}"
                                                    data-delete-url="{{ route('admin.departments.destroy', $department) }}">
                                                    <i class="fas fa-trash me-2"></i> Delete
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No departments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the department <strong id="modalDeptName"></strong>?</p>
                    <p class="text-danger small mb-0"><i class="fas fa-exclamation-triangle me-1"></i> This action cannot be
                        undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteDepartmentForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i> Delete Department
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var deleteModal = document.getElementById('deleteDepartmentModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var deptName = button.getAttribute('data-dept-name');
                var deleteUrl = button.getAttribute('data-delete-url');

                var modalDeptName = deleteModal.querySelector('#modalDeptName');
                var deleteForm = deleteModal.querySelector('#deleteDepartmentForm');

                modalDeptName.textContent = deptName;
                deleteForm.action = deleteUrl;
            });
        });
    </script>
@endsection