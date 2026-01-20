@extends('admin.layouts.app')

@section('title', 'Lecturer Dashboard')

@section('content')
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <span class="badge badge-soft mb-2">Lecturer</span>
            <h1 class="h2 section-title">My Workload Portfolio</h1>
            <p class="text-muted mb-0">
                View your assigned TaskForce, workload summary, and submit remarks to your HOD.
                @if(isset($currentSession))
                    <span class="badge bg-primary ms-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $currentSession->academic_year }} - Semester {{ $currentSession->semester }}
                    </span>
                @endif
            </p>
        </div>
        <div class="col-md-4">
            <div class="card text-white border-0 shadow-sm"
                style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50 fw-bold text-uppercase">Current User</small>
                            <h5 class="mb-0 fw-bold">{{ Auth::user()->name }}</h5>
                            <small>Lecturer</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-chalkboard-teacher fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Workload Status Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Current Status</p>
                            <h3
                                class="mb-0 fw-bold {{ str_contains($statusColor, 'green') ? 'text-success' : (str_contains($statusColor, 'yellow') ? 'text-warning' : 'text-danger') }}">
                                {{ $status }}
                            </h3>
                        </div>
                        <div
                            class="bg-{{ str_contains($statusColor, 'green') ? 'success' : (str_contains($statusColor, 'yellow') ? 'warning' : 'danger') }} bg-opacity-10 rounded-circle p-3">
                            <i
                                class="fas fa-chart-line fa-lg text-{{ str_contains($statusColor, 'green') ? 'success' : (str_contains($statusColor, 'yellow') ? 'warning' : 'danger') }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Weightage Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Total Workload</p>
                            <h3 class="mb-0 fw-bold text-primary">{{ $totalWorkload }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-weight-hanging fa-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Tasks Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Active TaskForce</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $activeTasksCount }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-tasks fa-lg text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="row g-4 mb-5">
        <!-- Assigned Taskforces -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-briefcase fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Assigned TaskForce</h5>
                    <p class="card-text text-muted small">View the TaskForce you are currently a member of.</p>
                    <a href="{{ route('workload.assigned-task-forces') }}"
                        class="btn btn-outline-primary btn-sm stretched-link">View Portfolio</a>
                </div>
            </div>
        </div>

        <!-- Workload Summary -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-pie fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Workload Summary</h5>
                    <p class="card-text text-muted small">Analyze your current workload breakdown and status.</p>
                    <a href="{{ route('workload.summary') }}" class="btn btn-outline-success btn-sm stretched-link">View
                        Summary</a>
                </div>
            </div>
        </div>

        <!-- Submit Remarks -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-comment-alt fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Submit Remarks</h5>
                    <p class="card-text text-muted small">Send feedback or concerns to your Head of Department.</p>
                    <a href="{{ route('workload.remarks') }}" class="btn btn-outline-warning btn-sm stretched-link">Open
                        Form</a>
                </div>
            </div>
        </div>

        <!-- History & Reports -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-info bg-opacity-10 text-info mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-history fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">History & Reports</h5>
                    <p class="card-text text-muted small">Access historical records or download official summaries.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('workload.history') }}" class="btn btn-outline-info btn-sm">History</a>
                        <a href="{{ route('workload.summary.download') }}" target="_blank"
                            class="btn btn-outline-secondary btn-sm">PDF</a>
                    </div>
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