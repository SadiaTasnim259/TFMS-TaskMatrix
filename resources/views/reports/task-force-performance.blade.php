@extends('layouts.app')

@section('title', 'TaskForce Performance Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">TaskForce Performance Report</h1>
            <p class="text-muted">{{ $taskForce->name }} - {{ $academicYear }} Semester {{ $semester }}</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-danger" onclick="window.print()">
                <i class="fas fa-file-pdf"></i> Print
            </button>
            <a href="{{ route('reports.create') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Task Force Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-users"></i> TaskForce Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Name:</strong><br>
                    {{ $taskForce->name }}
                </div>
                <div class="col-md-3">
                    <strong>Status:</strong><br>
                    <span class="badge bg-{{ $taskForce->is_active ? 'success' : 'secondary' }}">
                        {{ $taskForce->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="col-md-3">
                    <strong>Lead Department:</strong><br>
                    {{ $taskForce->leadDepartment->name ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Chair:</strong><br>
                    {{ $taskForce->chair->user->name ?? 'N/A' }}
                </div>
            </div>
            @if($taskForce->objective)
                <div class="mt-3">
                    <strong>Objective:</strong><br>
                    <p class="mb-0">{{ $taskForce->objective }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- KPI Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Participating Departments</h6>
                    <h2>{{ $kpis['total_departments'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Total Members</h6>
                    <h2>{{ $kpis['total_members'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Active Projects</h6>
                    <h2>{{ $kpis['active_projects'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Completion Rate</h6>
                    <h2>{{ number_format($kpis['completion_rate'] ?? 0, 1) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Participation -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-building"></i> Department Participation</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Department</th>
                            <th>Members Count</th>
                            <th>Contribution Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departmentParticipation as $dept)
                            <tr>
                                <td>{{ $dept['department_name'] }}</td>
                                <td>{{ $dept['members_count'] }}</td>
                                <td>
                                    <div class="progress" style="height: 25px;">
                                        @php
                                            $percentage = $kpis['total_members'] > 0 ? ($dept['members_count'] / $kpis['total_members']) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $dept['is_active'] ? 'success' : 'secondary' }}">
                                        {{ $dept['is_active'] ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Members List -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user-friends"></i> TaskForce Members</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Chairperson:</strong><br>
                    {{ $taskForce->chair->name ?? 'N/A' }}
                </div>
            </div>
            <div class="mt-3">
                <strong>Members:</strong>
                @if($taskForce->members->count() > 0)
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No members assigned to this TaskForce</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-chart-line"></i> Performance Metrics</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Achievements This Period</h6>
                    @if(!empty($achievements))
                        <ul class="list-group">
                            @foreach($achievements as $achievement)
                                <li class="list-group-item">
                                    <i class="fas fa-check-circle text-success"></i> {{ $achievement }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No achievements recorded for this period</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6>Key Performance Indicators</h6>
                    <table class="table table-sm">
                        <tr>
                            <td>Meeting Frequency</td>
                            <td><strong>{{ $kpis['meeting_frequency'] ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Deliverables Completed</td>
                            <td><strong>{{ $kpis['deliverables_completed'] ?? 0 }}</strong></td>
                        </tr>
                        <tr>
                            <td>Budget Utilization</td>
                            <td><strong>{{ number_format($kpis['budget_utilization'] ?? 0, 1) }}%</strong></td>
                        </tr>
                        <tr>
                            <td>Stakeholder Satisfaction</td>
                            <td><strong>{{ number_format($kpis['stakeholder_satisfaction'] ?? 0, 1) }}/5.0</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
