@extends('admin.layouts.app')

@section('title', 'Create Task Force')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Create New Task Force</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.task-forces.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Task Force ID -->
                    <!-- Task Force ID -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Task Force ID</label>
                        <input type="text" class="form-control" value="Auto-generated" disabled readonly>
                        <small class="form-text text-muted">Will be generated automatically (e.g.,
                            TF-{{ date('Y') }}-XXXXX)</small>
                    </div>

                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Task Force Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                            value="{{ old('name') }}" placeholder="e.g., Curriculum Development Committee" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Academic Year -->
                    <div class="col-md-6 mb-3">
                        <label for="academic_year" class="form-label">Academic Session <span class="text-danger">*</span></label>
                        <select class="form-control @error('academic_year') is-invalid @enderror" 
                                id="academic_year" name="academic_year" required>
                            <option value="">-- Select Session --</option>
                            @foreach($academicSessions as $session)
                                <option value="{{ $session->academic_year }}" 
                                    {{ (old('academic_year') == $session->academic_year) || (!old('academic_year') && isset($currentSession) && $currentSession->academic_year == $session->academic_year && $session->is_active) ? 'selected' : '' }}>
                                    {{ $session->academic_year }} - Semester {{ $session->semester }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Default Weightage -->
                    <div class="col-md-6 mb-3">
                        <label for="default_weightage" class="form-label">Default Weightage <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="1.0" max="{{ $maxWorkload }}"
                            class="form-control @error('default_weightage') is-invalid @enderror" id="default_weightage"
                            name="default_weightage" value="{{ old('default_weightage', 2.00) }}" required>
                        <small class="form-text text-muted">Value between 1.0 and {{ $maxWorkload }}</small>
                        @error('default_weightage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

    <!-- Description -->
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="4"
            placeholder="Brief description of the task force objectives and responsibilities">{{ old('description') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        <!-- Owner/Chair -->
        <!-- Task Force Chair/Owner Removed -->

        <!-- Active Status -->
        <div class="col-md-6 mb-3">
            <label class="form-label d-block">Status</label>
            <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" @if(old('active', true))
                checked @endif>
                <label class="form-check-label" for="active">
                    Active (Task force is currently operational)
                </label>
            </div>
        </div>
    </div>



    <!-- Buttons -->
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Task Force
        </button>
        <a href="{{ route('admin.task-forces.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
    </form>
    </div>
    </div>
@endsection