@extends('layouts.app')

@section('title', isset($academicSession) ? 'Edit Academic Session' : 'New Academic Session')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ isset($academicSession) ? 'Edit Session' : 'Create New Session' }}</h1>
        <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ isset($academicSession) ? route('admin.academic-sessions.update', $academicSession) : route('admin.academic-sessions.store') }}" method="POST">
                @csrf
                @if(isset($academicSession))
                    @method('PUT')
                @endif

                <div class="row">
                    <!-- Academic Year -->
                    <div class="col-md-6 mb-3">
                        <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror" 
                            id="academic_year" name="academic_year" 
                            value="{{ old('academic_year', $academicSession->academic_year ?? '2025/2026') }}" 
                            placeholder="YYYY/YYYY" required>
                        <div class="form-text">Format: 2024/2025</div>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Semester -->
                    <div class="col-md-6 mb-3">
                        <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select @error('semester') is-invalid @enderror" id="semester" name="semester" required>
                            <option value="1" {{ old('semester', $academicSession->semester ?? '') == 1 ? 'selected' : '' }}>Semester 1</option>
                            <option value="2" {{ old('semester', $academicSession->semester ?? '') == 2 ? 'selected' : '' }}>Semester 2</option>
                        </select>
                        @error('semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Start Date -->
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                            id="start_date" name="start_date" 
                            value="{{ old('start_date', isset($academicSession) ? $academicSession->start_date->format('Y-m-d') : '') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                            id="end_date" name="end_date" 
                            value="{{ old('end_date', isset($academicSession) ? $academicSession->end_date->format('Y-m-d') : '') }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="planning" {{ old('status', $academicSession->status ?? '') == 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="published" {{ old('status', $academicSession->status ?? '') == 'published' ? 'selected' : '' }}>Published (Active)</option>
                            <option value="archived" {{ old('status', $academicSession->status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Is Active -->
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $academicSession->is_active ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">
                                Set as Current Active Session
                            </label>
                            <div class="form-text">This will deactivate any currently active session.</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> {{ isset($academicSession) ? 'Update Session' : 'Create Session' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
