@extends('layouts.app')

@section('title', 'Department Workload Report')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Department Workload Report</h1>
                <p class="text-muted">{{ $department->name }} - {{ $report->academic_year }} Semester
                    {{ $report->semester }}</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-danger" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i> Print PDF
                </button>
                <a href="{{ route('reports.create') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Department Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-building"></i> Department Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Department:</strong><br>
                        {{ $department->name }}
                    </div>
                    <div class="col-md-4">
                        <strong>Code:</strong><br>
                        {{ $department->code }}
                    </div>
                    <div class="col-md-4">
                        <strong>Total Staff:</strong><br>
                        {{ $totalStaff }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Aggregate Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6>Total Submissions</h6>
                        <h2>{{ $summary['total_submissions'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6>Total Hours</h6>
                        <h2>{{ number_format($summary['total_hours'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6>Total Credits</h6>
                        <h2>{{ number_format($summary['total_credits'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6>Avg Hours/Staff</h6>
                        <h2>{{ number_format($summary['avg_hours_per_staff'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Type Breakdown -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Activity Type Breakdown</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Activity Type</th>
                                <th>Count</th>
                                <th>Total Hours</th>
                                <th>Total Credits</th>
                                <th>% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityBreakdown as $type => $data)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $data['color'] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </span>
                                    </td>
                                    <td>{{ $data['count'] }}</td>
                                    <td>{{ number_format($data['hours'], 2) }}</td>
                                    <td>{{ number_format($data['credits'], 2) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $data['percentage'] }}%">
                                                {{ number_format($data['percentage'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Staff Workload Distribution -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users"></i> Staff Workload Distribution</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Staff Name</th>
                                <th>Staff ID</th>
                                <th>Submissions</th>
                                <th>Total Hours</th>
                                <th>Total Credits</th>
                                <th>Activities</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffWorkloads as $workload)
                                <tr>
                                    <td>{{ $workload['staff_name'] }}</td>
                                    <td>{{ $workload['staff_id'] }}</td>
                                    <td>{{ $workload['submissions_count'] }}</td>
                                    <td>{{ number_format($workload['total_hours'], 2) }}</td>
                                    <td>{{ number_format($workload['total_credits'], 2) }}</td>
                                    <td>{{ $workload['activities_count'] }}</td>
                                    <td>
                                        @if($workload['has_pending'])
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($workload['all_approved'])
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-secondary">Mixed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2">Total</th>
                                <th>{{ collect($staffWorkloads)->sum('submissions_count') }}</th>
                                <th>{{ number_format(collect($staffWorkloads)->sum('total_hours'), 2) }}</th>
                                <th>{{ number_format(collect($staffWorkloads)->sum('total_credits'), 2) }}</th>
                                <th>{{ collect($staffWorkloads)->sum('activities_count') }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection