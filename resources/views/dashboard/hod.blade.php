@extends('admin.layouts.app')

@section('title', 'HOD Dashboard')

@section('content')
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <span class="badge badge-soft mb-2">Head of Department</span>
            <h1 class="h2 section-title">Department Leadership</h1>
            <p class="text-muted">Balance workload, curate task force membership, and submit to PSM with confidence.</p>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-dark-50 fw-bold text-uppercase">Current User</small>
                            <h5 class="mb-0 fw-bold">{{ Auth::user()->name }}</h5>
                            <small>Department View</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-user-tie fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="row g-4 mb-5">
        <!-- Department Workload -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-balance-scale fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Department Workload</h5>
                    <p class="card-text text-muted small">Review staff workload, fairness status, and drill into
                        individuals.</p>
                    <a href="{{ route('hod.workload.index') }}" class="btn btn-outline-primary btn-sm stretched-link">Open
                        Workload View</a>
                </div>
            </div>
        </div>

        <!-- Task Force Assignments -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Task Force Assignments</h5>
                    <p class="card-text text-muted small">Assign members, adjust roles, and balance under-loaded staff.</p>
                    <a href="{{ route('hod.task-forces.index') }}"
                        class="btn btn-outline-warning btn-sm stretched-link">Manage Task Forces</a>
                </div>
            </div>
        </div>

        <!-- Reports & Analytics -->
        {{--
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-info bg-opacity-10 text-info mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-bar fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Reports & Analytics</h5>
                    <p class="card-text text-muted small">Generate department reports and track approval readiness.</p>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-info btn-sm stretched-link">View
                        Reports</a>
                </div>
            </div>
        </div>
        --}}
    </div>
@endsection