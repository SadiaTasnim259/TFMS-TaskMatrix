@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Reports</h1>
                <p class="text-muted">Generate and view reports for workload analysis and performance evaluation</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('reports.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Generate New Report
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Reports</h6>
                        <h2 class="mb-0">{{ $totalReports }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">This Month</h6>
                        <h2 class="mb-0">{{ $monthlyReports }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Departments</h6>
                        <h2 class="mb-0">{{ $departmentCount }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Staff Tracked</h6>
                        <h2 class="mb-0">{{ $staffCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filters
            </div>
            <div class="card-body">
                <form action="{{ route('reports.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Report Type</label>
                            <select name="report_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="staff-workload" {{ request('report_type') == 'staff-workload' ? 'selected' : '' }}>
                                    Staff Workload
                                </option>
                                <option value="department-workload" {{ request('report_type') == 'department-workload' ? 'selected' : '' }}>
                                    Department Workload
                                </option>
                                <option value="performance-evaluation" {{ request('report_type') == 'performance-evaluation' ? 'selected' : '' }}>
                                    Performance Evaluation
                                </option>
                                <option value="task-force-performance" {{ request('report_type') == 'task-force-performance' ? 'selected' : '' }}>
                                    Task Force Performance
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year" class="form-select">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select">
                                <option value="">All Semesters</option>
                                <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                                <option value="3" {{ request('semester') == '3' ? 'selected' : '' }}>Semester 3</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reports List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Generated Reports</h5>
            </div>
            <div class="card-body">
                @if($reports->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Report Type</th>
                                    <th>Period</th>
                                    <th>Department</th>
                                    <th>Generated By</th>
                                    <th>Generated At</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    <tr>
                                        <td>
                                            <span
                                                class="badge bg-{{ $report->report_type == 'staff_workload' ? 'primary' : ($report->report_type == 'department_workload' ? 'info' : ($report->report_type == 'performance_evaluation' ? 'success' : 'warning')) }}">
                                                {{ $report->getTypeLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $report->academic_year }} - Semester {{ $report->semester }}
                                        </td>
                                        <td>{{ $report->department->name ?? 'All' }}</td>
                                        <td>{{ $report->generatedBy->name ?? 'System' }}</td>
                                        <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        {{ $reports->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No reports found</p>
                        <a href="{{ route('reports.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Generate Your First Report
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection