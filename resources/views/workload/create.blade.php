@extends('layouts.app')

@section('title', 'Create Workload Submission')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="h2">Create Workload Submission</h1>
                <nav aria-label="breadcrumb">

                </nav>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('workload.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Left Column: Basic Info -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Submission Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <select name="academic_year" class="form-select" required>
                                        <option value="{{ $currentYear }}" selected>{{ $currentYear }}
                                            {{ isset($activeSession) && $activeSession->is_active ? '(Active)' : '' }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select name="semester" class="form-select" required>
                                        <option value="">Select Semester</option>
                                        <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                                        <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" class="form-control" rows="3"
                                        placeholder="Add any additional information about this submission">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary & Actions -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <small>You can add activities after creating the submission</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Submission
                                </button>
                                <a href="{{ route('workload.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-question-circle"></i> Need Help?</h6>
                        </div>
                        <div class="card-body">
                            <small>
                                <p class="mb-2"><strong>Step 1:</strong> Create the submission</p>
                                <p class="mb-2"><strong>Step 2:</strong> Add your activities</p>
                                <p class="mb-2"><strong>Step 3:</strong> Review and submit</p>
                                <p class="mb-0"><strong>Step 4:</strong> Wait for approval</p>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection