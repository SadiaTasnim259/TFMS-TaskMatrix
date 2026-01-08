@extends('layouts.app')

@section('title', isset($score) ? 'Edit Performance Evaluation' : 'Create Performance Evaluation')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">{{ isset($score) ? 'Edit' : 'Create' }} Performance Evaluation</h1>
                <nav aria-label="breadcrumb">

                </nav>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <h5><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ isset($score) ? route('performance.update', $score) : route('performance.store') }}" method="POST">
            @csrf
            @if(isset($score))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-8">
                    <!-- Staff & Period Selection -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Evaluation Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                                    <select class="form-select @error('staff_id') is-invalid @enderror" id="staff_id"
                                        name="staff_id" required>
                                        <option value="">Select Staff Member</option>
                                        @foreach($staff as $s)
                                            <option value="{{ $s->id }}" {{ (old('staff_id', $performanceScore->staff_id ?? null) == $s->id) ? 'selected' : '' }}>
                                                {{ $s->name }} ({{ $s->employee_id ?? $s->id }}) -
                                                {{ $s->department->name ?? 'No Dept' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(isset($score))
                                        <input type="hidden" name="staff_id" value="{{ $score->staff_id }}">
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <select name="academic_year" class="form-select" required>
                                        <option value="">Select Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year }}" {{ (old('academic_year') ?? ($score->academic_year ?? '')) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select name="semester" class="form-select" required>
                                        <option value="">Select Semester</option>
                                        <option value="1" {{ (old('semester') ?? ($score->semester ?? '')) == '1' ? 'selected' : '' }}>Semester 1</option>
                                        <option value="2" {{ (old('semester') ?? ($score->semester ?? '')) == '2' ? 'selected' : '' }}>Semester 2</option>
                                        <option value="3" {{ (old('semester') ?? ($score->semester ?? '')) == '3' ? 'selected' : '' }}>Semester 3</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Evaluation Date <span class="text-danger">*</span></label>
                                    <input type="date" name="evaluation_date" class="form-control" required
                                        value="{{ old('evaluation_date') ?? ($score->evaluation_date->format('Y-m-d') ?? date('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Scores -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-line"></i> Performance Scores</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Teaching Effectiveness (Max: 40) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="teaching_effectiveness" class="form-control" step="0.5"
                                        min="0" max="40" required
                                        value="{{ old('teaching_effectiveness') ?? ($score->teaching_effectiveness ?? '') }}"
                                        oninput="calculateTotal()">
                                    <small class="text-muted">Evaluate course delivery, student engagement, assessment
                                        quality</small>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Research Contribution (Max: 30) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="research_contribution" class="form-control" step="0.5"
                                        min="0" max="30" required
                                        value="{{ old('research_contribution') ?? ($score->research_contribution ?? '') }}"
                                        oninput="calculateTotal()">
                                    <small class="text-muted">Publications, grants, research projects, supervision</small>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Service Activities (Max: 30) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="service_activities" class="form-control" step="0.5" min="0"
                                        max="30" required
                                        value="{{ old('service_activities') ?? ($score->service_activities ?? '') }}"
                                        oninput="calculateTotal()">
                                    <small class="text-muted">Committee work, administrative tasks, community
                                        service</small>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Comments (Optional)</label>
                                    <textarea name="comments" class="form-control" rows="4"
                                        placeholder="Additional comments about performance">{{ old('comments') ?? ($score->comments ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="col-md-4">
                    <!-- Score Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calculator"></i> Score Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted">Teaching Effectiveness</small>
                                <h4 class="text-primary mb-0" id="display-teaching">0.00</h4>
                                <small class="text-muted">of 40</small>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted">Research Contribution</small>
                                <h4 class="text-info mb-0" id="display-research">0.00</h4>
                                <small class="text-muted">of 30</small>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted">Service Activities</small>
                                <h4 class="text-success mb-0" id="display-service">0.00</h4>
                                <small class="text-muted">of 30</small>
                            </div>
                            <div>
                                <small class="text-muted">Total Score</small>
                                <h2 class="text-warning mb-0" id="display-total">0.00</h2>
                                <small class="text-muted">of 100</small>
                            </div>
                            <hr>
                            <div>
                                <small class="text-muted">Predicted Rating</small>
                                <h4 id="display-rating" class="mb-0">
                                    <span class="badge bg-secondary">Not Rated</span>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Guide -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-star"></i> Rating Guide</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <span class="badge bg-success">Outstanding</span>
                                    <small class="text-muted d-block">90-100 points</small>
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-primary">Excellent</span>
                                    <small class="text-muted d-block">80-89 points</small>
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-info">Good</span>
                                    <small class="text-muted d-block">70-79 points</small>
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-warning">Satisfactory</span>
                                    <small class="text-muted d-block">60-69 points</small>
                                </li>
                                <li>
                                    <span class="badge bg-danger">Needs Improvement</span>
                                    <small class="text-muted d-block">Below 60 points</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="card">
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ isset($score) ? 'Update' : 'Create' }} Evaluation
                    </button>
                    <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script>
        function calculateTotal() {
            const teaching = parseFloat(document.querySelector('[name="teaching_effectiveness"]').value) || 0;
            const research = parseFloat(document.querySelector('[name="research_contribution"]').value) || 0;
            const service = parseFloat(document.querySelector('[name="service_activities"]').value) || 0;
            const total = teaching + research + service;

            // Update displays
            document.getElementById('display-teaching').textContent = teaching.toFixed(2);
            document.getElementById('display-research').textContent = research.toFixed(2);
            document.getElementById('display-service').textContent = service.toFixed(2);
            document.getElementById('display-total').textContent = total.toFixed(2);

            // Calculate rating
            let rating = 'Not Rated';
            let badgeClass = 'bg-secondary';

            if (total >= 90) {
                rating = 'Outstanding';
                badgeClass = 'bg-success';
            } else if (total >= 80) {
                rating = 'Excellent';
                badgeClass = 'bg-primary';
            } else if (total >= 70) {
                rating = 'Good';
                badgeClass = 'bg-info';
            } else if (total >= 60) {
                rating = 'Satisfactory';
                badgeClass = 'bg-warning';
            } else if (total > 0) {
                rating = 'Needs Improvement';
                badgeClass = 'bg-danger';
            }

            document.getElementById('display-rating').innerHTML = `<span class="badge ${badgeClass}">${rating}</span>`;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', calculateTotal);
    </script>
@endsection