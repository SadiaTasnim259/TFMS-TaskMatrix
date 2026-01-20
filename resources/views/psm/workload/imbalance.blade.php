@extends('layouts.app')

@section('title', 'Faculty Workload Imbalance Report')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
            <div>
                <h1>Faculty Workload Imbalance Report</h1>

            </div>
            <a href="{{ route('psm.workload.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Overview
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-danger text-white mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">{{ $stats['overloaded'] }}</h2>
                            <div>Overloaded Staff (> {{ $maxWeightage }})</div>
                        </div>
                        <i class="fas fa-exclamation-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-warning text-dark mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">{{ $stats['underloaded'] }}</h2>
                            <div>Under-loaded Staff (< {{ $minWeightage }})</div>
                        </div>
                        <i class="fas fa-battery-quarter fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Imbalanced Staff Table -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-balance-scale me-1"></i>
                Staff Requiring Attention
            </div>
            <div class="card-body">
                @if($imbalancedStaff->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <h4>All Balanced!</h4>
                        <p class="text-muted">There are no staff members with workload imbalances at this time.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff Member</th>
                                    <th>Department</th>
                                    <th>Total Weightage</th>
                                    <th>Status</th>
                                    <th>TaskForce</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($imbalancedStaff as $staff)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $staff->first_name }} {{ $staff->last_name }}</div>
                                                    <small class="text-muted">{{ $staff->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($staff->department)
                                                <span class="badge bg-light text-dark border">{{ $staff->department->code }}</span>
                                                <div class="small text-muted mt-1">{{ $staff->department->name }}</div>
                                            @else
                                                <span class="text-muted">No Dept</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold fs-5">{{ $staff->total_workload }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match ($staff->workload_status) {
                                                    'Under-loaded' => 'bg-warning text-dark',
                                                    'Overloaded' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $staff->workload_status }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ $staff->taskForces->count() }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('psm.workload.show', $staff->department_id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-search me-1"></i> Review Dept
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection