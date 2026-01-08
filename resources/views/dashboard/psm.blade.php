@extends('admin.layouts.app')

@section('title', 'PSM Dashboard')

@section('content')
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <span class="badge badge-soft mb-2">PSM / HR Officer</span>
            <h1 class="h2 section-title">Faculty Balancing Hub</h1>
            <p class="text-muted">Monitor workload distribution, approve departmental submissions, and export faculty
                insights.</p>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50 fw-bold text-uppercase">Current User</small>
                            <h5 class="mb-0 fw-bold">{{ Auth::user()->name }}</h5>
                            <small>PSM / HR</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-user-cog fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="row g-4 mb-5">

        <!-- 1. View Faculty Taskforces -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-purple bg-opacity-10 text-purple mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px; color: #6610f2;">
                        <i class="fas fa-project-diagram fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">View Faculty Taskforces</h5>
                    <p class="card-text text-muted small">Access the master directory of all task forces.</p>
                    <a href="{{ route('psm.task-forces.index') }}" class="btn btn-outline-primary btn-sm stretched-link"
                        style="border-color: #6610f2; color: #6610f2;">View Directory</a>
                </div>
            </div>
        </div>

        <!-- 2. Review Dept. Submissions -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-clipboard-check fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Review Dept. Submissions</h5>
                    <p class="card-text text-muted small">Approve or reject pending membership requests.</p>
                    <a href="{{ route('psm.task-forces.requests') }}"
                        class="btn btn-outline-warning btn-sm stretched-link">Review Requests</a>
                </div>
            </div>
        </div>

        <!-- 3. Generate Faculty Reports -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-file-invoice fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Generate Faculty Reports</h5>
                    <p class="card-text text-muted small">Export PDF/Excel reports for workload and task forces.</p>
                    <a href="{{ route('psm.reports.index') }}"
                        class="btn btn-outline-success btn-sm stretched-link">Generate Reports</a>
                </div>
            </div>
        </div>

    </div>
@endsection