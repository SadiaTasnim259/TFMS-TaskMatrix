@extends('layouts.app')

@section('title', 'Edit Staff - TFMS')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 section-title mb-0">Edit Staff: {{ $user->name }}</h1>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Staff List
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Basic Information Section -->
                        <div class="col-12">
                            <h5 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Basic Information
                            </h5>
                        </div>

                        <!-- Staff ID -->
                        <div class="col-md-6">
                            <label for="staff_id" class="form-label">Staff ID</label>
                            <input type="text" name="staff_id" id="staff_id" value="{{ $user->staff_id }}"
                                class="form-control bg-light" disabled readonly>
                            <div class="form-text text-muted">Staff ID cannot be changed.</div>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="form-control @error('name') is-invalid @enderror" required
                                placeholder="e.g. Dr. John Doe">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror" required
                                placeholder="john.doe@utm.my">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror"
                                required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Account
                                </label>
                            </div>
                        </div>





                        <!-- Department -->
                        <div class="col-md-6" id="department_group">
                            <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id"
                                class="form-select @error('department_id') is-invalid @enderror" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        <!-- Employment Status -->
                        <div class="col-md-6">
                            <label for="employment_status" class="form-label">Employment Status</label>
                            <select name="employment_status" id="employment_status"
                                class="form-select @error('employment_status') is-invalid @enderror">
                                <option value="Permanent" {{ old('employment_status', $user->employment_status) == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="Contract" {{ old('employment_status', $user->employment_status) == 'Contract' ? 'selected' : '' }}>Contract</option>
                                <option value="Visiting" {{ old('employment_status', $user->employment_status) == 'Visiting' ? 'selected' : '' }}>Visiting</option>
                                <option value="Inactive" {{ old('employment_status', $user->employment_status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('employment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="form-control">{{ old('notes', $user->notes) }}</textarea>
                        </div>

                        <div class="col-12 text-end mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light border me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Update Staff
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role_id');
            const departmentGroup = document.getElementById('department_group');
            const departmentSelect = document.getElementById('department_id');

            // Pass roles data to JS
            const roles = @json($roles);

            function updateDepartmentVisibility() {
                const selectedRoleId = roleSelect.value;
                const selectedRole = roles.find(r => r.id == selectedRoleId);

                if (selectedRole) {
                    const slug = selectedRole.slug;
                    // Roles that do NOT need department: admin, psm, management
                    if (['admin', 'psm', 'management', 'task-force-owner'].includes(slug)) {
                        departmentGroup.style.display = 'none';
                        departmentSelect.removeAttribute('required');
                        
                        // Only clear value if user interacts (optional, but safer to keep existing value on load if hidden? 
                        // Actually, if we want to validly submit, we should probably clear it or ensure backend ignores it.
                        // But for UI, let's behave like create: clear it if hidden to avoid confusion)
                        // However, on INITIAL load, if it's admin, it's likely null.
                        // Let's only clear on change.
                    } else if (['lecturer', 'hod'].includes(slug)) {
                         // Roles that NEED department
                        departmentGroup.style.display = 'block';
                        departmentSelect.setAttribute('required', 'required');
                    } else {
                         // Fallback for others
                        departmentGroup.style.display = 'none';
                        departmentSelect.removeAttribute('required');
                    }
                } else {
                     // Default state
                    departmentGroup.style.display = 'none';
                    departmentSelect.removeAttribute('required');
                }
            }

            roleSelect.addEventListener('change', function() {
                // If switching TO a non-department role, clear the selection
                const selectedRoleId = roleSelect.value;
                const selectedRole = roles.find(r => r.id == selectedRoleId);
                 if (selectedRole && ['admin', 'psm', 'management', 'task-force-owner'].includes(selectedRole.slug)) {
                    departmentSelect.value = ''; 
                 }
                updateDepartmentVisibility();
            });
            
            // Initial run
            updateDepartmentVisibility();
        });
    </script>
    @endsection
@endsection