@extends('admin.layouts.app')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <p class="text-uppercase text-muted fw-semibold small mb-1">Admin • Settings</p>
            <h1 class="h3 section-title mb-0">System Configuration</h1>
            <p class="text-muted mb-0">Academic session, thresholds, and default weightages.</p>
        </div>
    </div>



    <!-- Performance Thresholds -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Performance Thresholds</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Set minimum performance standards for the institution.</p>

            <form action="{{ route('admin.configuration.thresholds-update') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="min_gpa" class="form-label">Minimum GPA Required</label>
                        <input type="number" step="0.01" min="0" max="4.0"
                            class="form-control @error('min_gpa') is-invalid @enderror" id="min_gpa" name="min_gpa"
                            placeholder="e.g., 2.0" value="{{ old('min_gpa', $configurations['min_gpa'] ?? '2.0') }}">
                        @error('min_gpa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="min_attendance" class="form-label">Minimum Attendance (%)</label>
                        <input type="number" step="0.1" min="0" max="100"
                            class="form-control @error('min_attendance') is-invalid @enderror" id="min_attendance"
                            name="min_attendance" placeholder="e.g., 75"
                            value="{{ old('min_attendance', $configurations['min_attendance'] ?? '75') }}">
                        @error('min_attendance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="max_course_load" class="form-label">Maximum Course Load (Credits)</label>
                        <input type="number" min="1" class="form-control @error('max_course_load') is-invalid @enderror"
                            id="max_course_load" name="max_course_load" placeholder="e.g., 18"
                            value="{{ old('max_course_load', $configurations['max_course_load'] ?? '18') }}">
                        @error('max_course_load')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="max_workload_hours" class="form-label">Maximum Weekly Workload (Hours)</label>
                        <input type="number" min="1" class="form-control @error('max_workload_hours') is-invalid @enderror"
                            id="max_workload_hours" name="max_workload_hours" placeholder="e.g., 40"
                            value="{{ old('max_workload_hours', $configurations['max_workload_hours'] ?? '40') }}">
                        @error('max_workload_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Thresholds
                </button>
            </form>
        </div>
    </div>

    <!-- Task Force Weightages -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Task Force Weightages</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Configure weightage (importance) for each task force category in performance evaluation.
            </p>

            <form action="{{ route('admin.configuration.weightages-update') }}" method="POST">
                @csrf

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Task Force Category</th>
                                <th>Description</th>
                                <th style="width: 120px;">Weightage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>Academic</strong>
                                </td>
                                <td>Teaching quality, curriculum development</td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="10"
                                        class="form-control form-control-sm @error('academic_weight') is-invalid @enderror"
                                        name="academic_weight"
                                        value="{{ old('academic_weight', $configurations['academic_weight'] ?? '3.0') }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Research</strong>
                                </td>
                                <td>Research activities, publications</td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="10"
                                        class="form-control form-control-sm @error('research_weight') is-invalid @enderror"
                                        name="research_weight"
                                        value="{{ old('research_weight', $configurations['research_weight'] ?? '2.5') }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Accreditation</strong>
                                </td>
                                <td>Accreditation & compliance</td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="10"
                                        class="form-control form-control-sm @error('accreditation_weight') is-invalid @enderror"
                                        name="accreditation_weight"
                                        value="{{ old('accreditation_weight', $configurations['accreditation_weight'] ?? '2.5') }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Quality</strong>
                                </td>
                                <td>Quality assurance & improvement</td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="10"
                                        class="form-control form-control-sm @error('quality_weight') is-invalid @enderror"
                                        name="quality_weight"
                                        value="{{ old('quality_weight', $configurations['quality_weight'] ?? '2.0') }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Strategic</strong>
                                </td>
                                <td>Strategic planning & planning</td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="10"
                                        class="form-control form-control-sm @error('strategic_weight') is-invalid @enderror"
                                        name="strategic_weight"
                                        value="{{ old('strategic_weight', $configurations['strategic_weight'] ?? '2.5') }}">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Administrative</strong>
                                </td>
                                <td>Administrative tasks & support</td>
                                <td>
                                    <input type="number" step="0.5" min="0" max="10"
                                        class="form-control form-control-sm @error('admin_weight') is-invalid @enderror"
                                        name="admin_weight"
                                        value="{{ old('admin_weight', $configurations['admin_weight'] ?? '2.0') }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Weightages
                </button>
            </form>
        </div>
    </div>

    <!-- Performance Evaluation Weights -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Performance Evaluation Weights</h5>
            <!-- Button removed as configuration is done inline or via modal in this view -->
        </div>
        <div class="card-body">
            <p class="text-muted">Configure default weightage percentages for performance evaluation (Teaching, Research,
                Admin, Support).</p>
            <div class="row">
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <h6 class="text-primary font-weight-bold">Teaching</h6>
                        <div class="h4 mb-0">{{ \App\Models\Configuration::getValue('perf_weight_teaching', 40) }}%</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <h6 class="text-success font-weight-bold">Research</h6>
                        <div class="h4 mb-0">{{ \App\Models\Configuration::getValue('perf_weight_research', 30) }}%</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <h6 class="text-info font-weight-bold">Admin</h6>
                        <div class="h4 mb-0">{{ \App\Models\Configuration::getValue('perf_weight_admin', 20) }}%</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <h6 class="text-warning font-weight-bold">Support</h6>
                        <div class="h4 mb-0">{{ \App\Models\Configuration::getValue('perf_weight_support', 10) }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Summary -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Current Configuration Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Academic Session</h6>
                    <ul class="list-unstyled">
                        <li><strong>Academic Year:</strong> {{ $configurations['academic_year'] ?? 'Not set' }}</li>
                        <li><strong>Current Semester:</strong> {{ $configurations['current_semester'] ?? 'Not set' }}</li>
                        <li><strong>Session Start:</strong> {{ $configurations['session_start_date'] ?? 'Not set' }}</li>
                        <li><strong>Session End:</strong> {{ $configurations['session_end_date'] ?? 'Not set' }}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Performance Thresholds</h6>
                    <ul class="list-unstyled">
                        <li><strong>Min GPA:</strong> {{ $configurations['min_gpa'] ?? 'Not set' }}</li>
                        <li><strong>Min Attendance:</strong> {{ $configurations['min_attendance'] ?? 'Not set' }}%</li>
                        <li><strong>Max Course Load:</strong> {{ $configurations['max_course_load'] ?? 'Not set' }} credits
                        </li>
                        <li><strong>Max Workload:</strong> {{ $configurations['max_workload_hours'] ?? 'Not set' }}
                            hours/week</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection