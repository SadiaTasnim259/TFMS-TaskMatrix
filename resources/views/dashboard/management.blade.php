@extends('admin.layouts.app')

@section('title', 'Management Dashboard')

@section('content')
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <span class="badge badge-soft mb-2">Faculty Management</span>
            <h1 class="h2 section-title">Executive Dashboard</h1>
            <p class="text-muted">View faculty-wide summaries, compare departments, and export executive reports.</p>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50 fw-bold text-uppercase">Current User</small>
                            <h5 class="mb-0 fw-bold">{{ Auth::user()->name }}</h5>
                            <small>Management Access</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-user-tie fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Staff Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Total Staff</p>
                            <h3 class="mb-0 fw-bold text-primary">{{ $totalStaff }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-users fa-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Departments</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $totalDepartments }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-building fa-lg text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Forces Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Active Task Forces</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $activeTaskForces }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-project-diagram fa-lg text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="row g-4 mb-5">
        <!-- Workload Overview -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-pie fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Workload Overview</h5>
                    <p class="card-text text-muted small">Deep dive into faculty workload distribution and balance.</p>
                    <a href="{{ route('management.dashboard') }}" class="btn btn-outline-primary btn-sm stretched-link">View
                        Dashboard</a>
                </div>
            </div>
        </div>

        <!-- Taskforce Distribution -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-bar fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Taskforce Distribution</h5>
                    <p class="card-text text-muted small">Analyze task force allocation across categories.</p>
                    <a href="{{ route('management.task_distribution') }}"
                        class="btn btn-outline-success btn-sm stretched-link">View Analysis</a>
                </div>
            </div>
        </div>

        <!-- Department Comparison -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-balance-scale fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Dept. Comparison</h5>
                    <p class="card-text text-muted small">Compare performance metrics between departments.</p>
                    <a href="{{ route('management.department_comparison') }}"
                        class="btn btn-outline-warning btn-sm stretched-link">Compare Now</a>
                </div>
            </div>
        </div>

        <!-- Export Data -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-file-export fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Export Data</h5>
                    <p class="card-text text-muted small">Download comprehensive reports in CSV/Excel format.</p>
                    <a href="{{ route('management.export_reports') }}"
                        class="btn btn-outline-danger btn-sm stretched-link">Go to Export</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-card {
            transition: transform 0.2s ease-in-out;
        }

        .hover-card:hover {
            transform: translateY(-5px);
        }
    </style>
@endsection