@extends('admin.layouts.app')

@section('title', 'Create Staff - TFMS')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 section-title mb-0">Create New Staff</h1>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Staff List
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <!-- Basic Information Section -->
                        <div class="col-12">
                            <h5 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Basic Information
                            </h5>
                        </div>

                        <!-- Staff ID -->
                        <div class="col-md-6">
                            <label for="staff_id" class="form-label">Staff ID</label>
                            <input type="text" name="staff_id" id="staff_id" value="Auto-generated (e.g. S12345)"
                                class="form-control bg-light" disabled readonly>
                            <div class="form-text text-muted">System will generate unique ID automatically.</div>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" required
                                placeholder="e.g. Dr. John Doe">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
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
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Hint (Auto-generated/Temp?) -->
                        <!-- In the previous code, there was a manual input for password. Let's keep it but style it nicely. -->
                        <!-- Wait, in store method I saw $tempPassword = Str::random(12), but the form had an input. 
                                                 Actually, the store method I read earlier IGNORED the password input and generated one:
                                                 'password' => Hash::make($tempPassword),
                                                 So the input field is misleading if the controller ignores it.
                                                 However, looking at the previous Controller code I read in step 407:
                                                 $tempPassword = Str::random(12);
                                                 'password' => Hash::make($tempPassword),
                                                 Yes, it generates a random one. It does NOT use $request->password.
                                                 So I should REMOVE the password field to avoid confusion, or let the user know it will be auto-generated.
                                                 Actually, let's double check if I missed something.
                                                 The controller code:
                                                 $tempPassword = Str::random(12);
                                                 $user = User::create([ ... 'password' => Hash::make($tempPassword) ... ]);
                                                 Mail::to($user)->send(new NewUserAccountMail($user, $tempPassword));

                                                 So yes, the input field in the previous view was essentially useless or misleading.
                                                 I will remove it and add a note that password is auto-generated.
                                            -->








                        <div class="col-md-6" id="department_group">
                            <label for="department_id" class="form-label">Department <span
                                    class="text-danger">*</span></label>
                            <select name="department_id" id="department_id"
                                class="form-select @error('department_id') is-invalid @enderror" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
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
                                <option value="Permanent" {{ old('employment_status') == 'Permanent' ? 'selected' : '' }}>
                                    Permanent</option>
                                <option value="Contract" {{ old('employment_status') == 'Contract' ? 'selected' : '' }}>
                                    Contract</option>
                                <option value="Visiting" {{ old('employment_status') == 'Visiting' ? 'selected' : '' }}>
                                    Visiting</option>
                            </select>
                            @error('employment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Create Staff
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
                        departmentSelect.value = ''; // Clear value
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
                     // Default state if no role selected (Hide department according to user request)
                    departmentGroup.style.display = 'none';
                    departmentSelect.removeAttribute('required');
                }
            }

            roleSelect.addEventListener('change', updateDepartmentVisibility);
            
            // Initial run in case of old input
            updateDepartmentVisibility();
        });
    </script>
    @endsection
@endsection