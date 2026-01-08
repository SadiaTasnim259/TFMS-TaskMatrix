@extends('layouts.app')

@section('title', 'Generate Report')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Generate Report</h1>
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

        <div class="row">
            <div class="col-md-8">
                <!-- Report Type Selection -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-alt"></i> Select Report Type</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-primary h-100" id="report-staff"
                                    onclick="selectReport('staff-workload')" style="cursor: pointer;">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user fa-3x text-primary mb-3"></i>
                                        <h5>Staff Workload Report</h5>
                                        <p class="text-muted small">View individual staff member's workload submissions and
                                            activities</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info h-100" id="report-department"
                                    onclick="selectReport('department-workload')" style="cursor: pointer;">
                                    <div class="card-body text-center">
                                        <i class="fas fa-building fa-3x text-info mb-3"></i>
                                        <h5>Department Workload Report</h5>
                                        <p class="text-muted small">Aggregate workload data by department with comparisons
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success h-100" id="report-performance"
                                    onclick="selectReport('performance-evaluation')" style="cursor: pointer;">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                                        <h5>Performance Evaluation Report</h5>
                                        <p class="text-muted small">Staff performance scores, ratings, and trend analysis
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-warning h-100" id="report-taskforce"
                                    onclick="selectReport('task-force-performance')" style="cursor: pointer;">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-3x text-warning mb-3"></i>
                                        <h5>Task Force Performance Report</h5>
                                        <p class="text-muted small">Task force KPIs, department participation, and
                                            achievements</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Parameters Form -->
                <form action="#" method="POST" id="reportForm" style="display: none;">
                    @csrf
                    <input type="hidden" name="report_type" id="reportType">

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cog"></i> Report Parameters</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <select name="academic_year" class="form-select" required>
                                        <option value="">Select Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year }}" {{ old('academic_year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select name="semester" class="form-select" required>
                                        <option value="">Select Semester</option>
                                        <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                                        <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                                        <option value="3" {{ old('semester') == '3' ? 'selected' : '' }}>Semester 3</option>
                                    </select>
                                </div>

                                <!-- Staff-specific field -->
                                <div class="col-md-12" id="staff-field" style="display: none;">
                                    <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                                    <select name="staff_id" class="form-select">
                                        <option value="">Select Staff</option>
                                        @foreach($staff as $s)
                                            <option value="{{ $s->id }}" {{ old('staff_id') == $s->id ? 'selected' : '' }}>
                                                {{ $s->name }} ({{ $s->department->name ?? 'No Dept' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Department-specific field -->
                                <div class="col-md-12" id="department-field" style="display: none;">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select name="department_id" class="form-select">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Task Force-specific field -->
                                <div class="col-md-12" id="taskforce-field" style="display: none;">
                                    <label class="form-label">Task Force <span class="text-danger">*</span></label>
                                    <select name="task_force_id" class="form-select">
                                        <option value="">Select Task Force</option>
                                        @foreach($taskForces as $tf)
                                            <option value="{{ $tf->id }}" {{ old('task_force_id') == $tf->id ? 'selected' : '' }}>
                                                {{ $tf->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Output Format</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="format" value="view"
                                                id="format-view" checked>
                                            <label class="form-check-label" for="format-view">
                                                View in Browser
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="format" value="pdf"
                                                id="format-pdf">
                                            <label class="form-check-label" for="format-pdf">
                                                Download PDF
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="format" value="excel"
                                                id="format-excel">
                                            <label class="form-check-label" for="format-excel">
                                                Download Excel
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Help Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Report Types</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-primary">Staff Workload Report</h6>
                        <p class="small text-muted">Shows detailed workload breakdown for a specific staff member including
                            all activities, hours, and credits.</p>

                        <h6 class="text-info mt-3">Department Workload Report</h6>
                        <p class="small text-muted">Aggregates workload data across a department, showing total hours, staff
                            distribution, and activity breakdown.</p>

                        <h6 class="text-success mt-3">Performance Evaluation Report</h6>
                        <p class="small text-muted">Displays performance scores, ratings, and trends for staff in a selected
                            period.</p>

                        <h6 class="text-warning mt-3">Task Force Performance Report</h6>
                        <p class="small text-muted">Shows task force achievements, department participation, KPIs, and
                            overall effectiveness.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectReport(type) {
            // Update hidden input
            document.getElementById('reportType').value = type;

            const form = document.getElementById('reportForm');
            form.style.display = 'block';

            // Set Form Action Route based on Type
            // Define routes in a JS object using Blade to interpret them
            const routes = {
                'staff-workload': "{{ route('reports.staff-workload') }}",
                'department-workload': "{{ route('reports.department-workload') }}",
                'performance-evaluation': "{{ route('reports.performance-report') }}",
                'task-force-performance': "{{ route('reports.task-force-report') }}"
            };

            if(routes[type]) {
                form.action = routes[type];
            } else {
                console.error("Unknown report type: " + type);
            }

            // Highlight selected card
            document.querySelectorAll('[id^="report-"]').forEach(card => {
                card.classList.remove('border-3');
            });
            document.getElementById('report-' + type.split('-')[0]).classList.add('border-3');

            // Show/hide specific fields
            document.getElementById('staff-field').style.display = 'none';
            document.getElementById('department-field').style.display = 'none';
            document.getElementById('taskforce-field').style.display = 'none';

            if (type === 'staff-workload') {
                document.getElementById('staff-field').style.display = 'block';
            } else if (type === 'department-workload') {
                document.getElementById('department-field').style.display = 'block';
            } else if (type === 'task-force-performance') {
                document.getElementById('taskforce-field').style.display = 'block';
            }

            // Scroll to form
            document.getElementById('reportForm').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
@endsection