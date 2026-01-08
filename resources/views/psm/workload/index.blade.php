@extends('layouts.app')

@section('title', 'Faculty Workload Overview')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Faculty Workload Overview</h1>


        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="h3 mb-0">{{ $stats['total_departments'] }}</div>
                                <div class="small">Total Departments</div>
                            </div>
                            <i class="fas fa-building fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="h3 mb-0">{{ $stats['submitted'] }}</div>
                                <div class="small">Submitted / Locked</div>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-dark mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="h3 mb-0">{{ $stats['pending'] }}</div>
                                <div class="small">Pending Submission</div>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('psm.workload.imbalance') }}" class="text-decoration-none">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="h5 mb-0">Imbalance Report</div>
                                    <div class="small">View Issues</div>
                                </div>
                                <i class="fas fa-balance-scale fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Departments Table -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Departmental Workload Status
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Head of Department</th>
                            <th>Staff Count</th>
                            <th>Task Forces</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $dept)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $dept->name }}</div>
                                    <small class="text-muted">{{ $dept->code }}</small>
                                </td>
                                <td>
                                    @if($dept->head)
                                        {{ $dept->head->first_name }} {{ $dept->head->last_name }}
                                    @else
                                        <span class="text-muted fst-italic">Not Assigned</span>
                                    @endif
                                </td>
                                <td>{{ $dept->staff_count }}</td>
                                <td>{{ $dept->task_forces_count }}</td>
                                <td>
                                    @if($dept->workload_locked)
                                        @if($dept->workload_status == 'Approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-info text-dark">Submitted</span>
                                        @endif
                                    @else
                                        @if($dept->workload_status == 'Rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{ $dept->workload_submitted_at ? $dept->workload_submitted_at->format('d M Y, h:i A') : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('psm.workload.show', $dept->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection