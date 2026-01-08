@extends('layouts.app')

@section('title', 'Performance Evaluations')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Performance Evaluations</h1>
                <p class="text-muted">Manage staff performance scores and evaluations</p>
            </div>
            <div class="col-md-4 text-end">
                @if(auth()->user()->isAdmin() || auth()->user()->isHOD())
                    <a href="{{ route('performance.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Evaluation
                    </a>
                @endif
            </div>
        </div>



        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Evaluations</h6>
                        <h2>{{ $totalEvaluations }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Average Score</h6>
                        <h2 class="text-success">{{ number_format($averageScore, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">This Month</h6>
                        <h2 class="text-primary">{{ $monthlyEvaluations }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Outstanding</h6>
                        <h2 class="text-warning">{{ $outstandingCount }}</h2>
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
                <form action="{{ route('performance.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
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
                                <option value="">All</option>
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
                        <div class="col-md-2">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-select">
                                <option value="">All Ratings</option>
                                <option value="Outstanding" {{ request('rating') == 'Outstanding' ? 'selected' : '' }}>
                                    Outstanding</option>
                                <option value="Excellent" {{ request('rating') == 'Excellent' ? 'selected' : '' }}>Excellent
                                </option>
                                <option value="Good" {{ request('rating') == 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Satisfactory" {{ request('rating') == 'Satisfactory' ? 'selected' : '' }}>
                                    Satisfactory</option>
                                <option value="Needs Improvement" {{ request('rating') == 'Needs Improvement' ? 'selected' : '' }}>Needs Improvement</option>
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

        <!-- Performance Scores Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Performance Scores</h5>
            </div>
            <div class="card-body">
                @if($scores->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff</th>
                                    <th>Department</th>
                                    <th>Period</th>
                                    <th>Teaching</th>
                                    <th>Research</th>
                                    <th>Service</th>
                                    <th>Total</th>
                                    <th>Rating</th>
                                    <th>Evaluated</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scores as $score)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-sm me-3 bg-primary rounded-circle text-white d-flex align-items-center justify-content-center">
                                                    {{ substr($score->staff->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $score->staff->name }}</strong><br>
                                                    <small
                                                        class="text-muted">{{ $score->staff->employee_id ?? $score->staff->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $score->staff->department->name ?? 'N/A' }}</td>
                                        <td>{{ $score->academic_year }} - S{{ $score->semester }}</td>
                                        <td>{{ number_format($score->teaching_effectiveness, 2) }}</td>
                                        <td>{{ number_format($score->research_contribution, 2) }}</td>
                                        <td>{{ number_format($score->service_activities, 2) }}</td>
                                        <td><strong>{{ number_format($score->total_score, 2) }}</strong></td>
                                        <td>
                                            @if($score->rating == 'Outstanding')
                                                <span class="badge bg-success">Outstanding</span>
                                            @elseif($score->rating == 'Excellent')
                                                <span class="badge bg-primary">Excellent</span>
                                            @elseif($score->rating == 'Good')
                                                <span class="badge bg-info">Good</span>
                                            @elseif($score->rating == 'Satisfactory')
                                                <span class="badge bg-warning">Satisfactory</span>
                                            @else
                                                <span class="badge bg-danger">Needs Improvement</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $score->evaluation_date->format('M d, Y') }}</small><br>
                                            <small class="text-muted">by {{ $score->evaluatedBy->name ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('performance.show', $score) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->user()->isAdmin() || auth()->user()->isHOD())
                                                <a href="{{ route('performance.edit', $score) }}"
                                                    class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $scores->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No performance evaluations found</p>
                        @if(auth()->user()->isAdmin() || auth()->user()->isHOD())
                            <a href="{{ route('performance.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Evaluation
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection