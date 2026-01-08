@extends('layouts.app')

@section('title', 'Lecturer Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1 text-gray-800">Welcome, {{ auth()->user()->name }}</h1>
            <p class="text-muted small">Here is an overview of your current workload and portfolio.</p>
        </div>
    </div>

    <!-- Status Overview Cards -->
    <div class="row mb-4">
        <!-- Workload Status Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div
                class="card border-left-{{ str_contains($statusColor, 'success') ? 'success' : (str_contains($statusColor, 'warning') ? 'warning' : 'danger') }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div
                                class="text-xs font-weight-bold text-{{ str_contains($statusColor, 'success') ? 'success' : (str_contains($statusColor, 'warning') ? 'warning' : 'danger') }} text-uppercase mb-1">
                                Current Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $status }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Weightage Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Workload Weightage
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalWorkload }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-weight-hanging fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Tasks Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Task Forces
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeTasksCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Grid -->
    <div class="row">
        <div class="col-12 mb-3">
            <h6 class="text-uppercase text-muted font-weight-bold ml-1">Quick Actions</h6>
        </div>

        <!-- Assigned Taskforces -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center h-100 shadow-sm border-0 bg-white hover-lift">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="rounded-circle bg-light p-3 mb-3 text-primary">
                        <i class="fas fa-briefcase fa-2x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold text-dark">Assigned Taskforces</h5>
                    <p class="card-text text-muted small mb-3">View the task forces you are currently a member of.</p>
                    <a href="{{ route('workload.assigned-task-forces') }}"
                        class="btn btn-sm btn-outline-primary mt-auto">View Portfolio</a>
                </div>
            </div>
        </div>

        <!-- Workload Summary -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center h-100 shadow-sm border-0 bg-white hover-lift">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="rounded-circle bg-light p-3 mb-3 text-success">
                        <i class="fas fa-chart-pie fa-2x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold text-dark">Workload Summary</h5>
                    <p class="card-text text-muted small mb-3">Analyze your current workload breakdown and status.</p>
                    <a href="{{ route('workload.summary') }}" class="btn btn-sm btn-outline-success mt-auto">View
                        Summary</a>
                </div>
            </div>
        </div>

        <!-- Submit Remarks -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center h-100 shadow-sm border-0 bg-white hover-lift">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="rounded-circle bg-light p-3 mb-3 text-warning">
                        <i class="fas fa-comment-alt fa-2x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold text-dark">Submit Remarks</h5>
                    <p class="card-text text-muted small mb-3">Send feedback or concerns to your Head of Department.</p>
                    <a href="{{ route('workload.remarks') }}" class="btn btn-sm btn-outline-warning mt-auto">Open Form</a>
                </div>
            </div>
        </div>

        <!-- More Actions (History / Download) -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center h-100 shadow-sm border-0 bg-white hover-lift">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="rounded-circle bg-light p-3 mb-3 text-info">
                        <i class="fas fa-history fa-2x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold text-dark">History & Reports</h5>
                    <p class="card-text text-muted small mb-3">Access historical records or download official summaries.</p>
                    <div class="btn-group mt-auto" role="group">
                        <a href="{{ route('workload.history') }}" class="btn btn-sm btn-outline-info">History</a>
                        <a href="{{ route('workload.summary.download') }}" target="_blank"
                            class="btn btn-sm btn-outline-secondary">Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }
    </style>
@endsection