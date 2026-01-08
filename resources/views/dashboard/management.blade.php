@extends('admin.layouts.app')

@section('title', 'Management Dashboard')

@section('content')
    <div class="row mb-5 align-items-center">
        <!-- Welcome Section -->
        <div class="col-lg-8">
            <span class="badge rounded-pill bg-white text-primary border shadow-sm px-3 py-2 mb-3 fw-bold text-uppercase"
                style="letter-spacing: 1px;">
                <i class="fas fa-user-tie me-2"></i>Executive Portal
            </span>
            <h1 class="display-5 fw-bold text-dark mb-2">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}</h1>
            <p class="text-muted fs-5 mb-0">Here's your executive summary for {{ date('l, F j, Y') }}.</p>
        </div>

        <!-- Quick Profile Card (Optional, or removed for cleaner look) -->
        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
            <div class="d-inline-flex align-items-center bg-white p-2 rounded-pill shadow-sm border">
                <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                    style="width: 48px; height: 48px;">
                    <span class="fw-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="text-start me-4">
                    <div class="fw-bold text-dark lh-1">{{ Auth::user()->name }}</div>
                    <small class="text-muted" style="font-size: 0.8rem;">Management Access</small>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Stats Row -->
    <div class="row g-4 mb-5">
        <!-- Staff Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success">+Active</span>
                    </div>
                    <h2 class="display-4 fw-bold mb-1 text-dark">{{ $totalStaff }}</h2>
                    <p class="text-muted mb-0 fw-medium">Total Staff Members</p>
                </div>
                <!-- Decorative Circle -->
                <div class="position-absolute top-0 end-0 translate-middle p-5 bg-primary opacity-10 rounded-circle filter-blur"
                    style="width: 150px; height: 150px; margin-top: -30px; margin-right: -30px;"></div>
            </div>
        </div>

        <!-- Departments Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                            <i class="fas fa-building fa-lg"></i>
                        </div>
                        <span class="badge bg-warning bg-opacity-10 text-warning">Structure</span>
                    </div>
                    <h2 class="display-4 fw-bold mb-1 text-dark">{{ $totalDepartments }}</h2>
                    <p class="text-muted mb-0 fw-medium">Active Departments</p>
                </div>
                <div class="position-absolute top-0 end-0 translate-middle p-5 bg-warning opacity-10 rounded-circle filter-blur"
                    style="width: 150px; height: 150px; margin-top: -30px; margin-right: -30px;"></div>
            </div>
        </div>

        <!-- Task Forces Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                            <i class="fas fa-project-diagram fa-lg"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info">Ongoing</span>
                    </div>
                    <h2 class="display-4 fw-bold mb-1 text-dark">{{ $activeTaskForces }}</h2>
                    <p class="text-muted mb-0 fw-medium">Active Task Forces</p>
                </div>
                <div class="position-absolute top-0 end-0 translate-middle p-5 bg-info opacity-10 rounded-circle filter-blur"
                    style="width: 150px; height: 150px; margin-top: -30px; margin-right: -30px;"></div>
            </div>
        </div>
    </div>

    <!-- Navigation / Launchpad -->
    <h5 class="fw-bold text-dark mb-4 px-1">
        <i class="fas fa-compass me-2 text-primary"></i>Quick Actions
    </h5>
    <div class="row g-4">
        <!-- Workload Overview -->
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('management.dashboard') }}"
                class="card h-100 border-0 shadow-hover text-decoration-none nav-card-link">
                <div class="card-body p-4">
                    <div class="mb-4 text-primary">
                        <i class="fas fa-chart-pie fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Workload Overview</h5>
                    <p class="text-muted small mb-0">Deep dive into faculty workload distribution and balance.</p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                    <span class="fw-bold text-primary small">View Dashboard <i class="fas fa-arrow-right ms-1"></i></span>
                </div>
            </a>
        </div>

        <!-- Taskforce Distribution -->
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('management.task_distribution') }}"
                class="card h-100 border-0 shadow-hover text-decoration-none nav-card-link">
                <div class="card-body p-4">
                    <div class="mb-4 text-success">
                        <i class="fas fa-chart-bar fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Taskforce Dist.</h5>
                    <p class="text-muted small mb-0">Analyze task force allocation across categories.</p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                    <span class="fw-bold text-success small">View Analysis <i class="fas fa-arrow-right ms-1"></i></span>
                </div>
            </a>
        </div>

        <!-- Department Comparison -->
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('management.department_comparison') }}"
                class="card h-100 border-0 shadow-hover text-decoration-none nav-card-link">
                <div class="card-body p-4">
                    <div class="mb-4 text-warning">
                        <i class="fas fa-balance-scale fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Dept. Comparison</h5>
                    <p class="text-muted small mb-0">Compare performance metrics between departments.</p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                    <span class="fw-bold text-warning small">Compare Now <i class="fas fa-arrow-right ms-1"></i></span>
                </div>
            </a>
        </div>

        <!-- Reports Export -->
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('management.export_reports') }}"
                class="card h-100 border-0 shadow-hover text-decoration-none nav-card-link">
                <div class="card-body p-4">
                    <div class="mb-4 text-danger">
                        <i class="fas fa-file-export fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Export Data</h5>
                    <p class="text-muted small mb-0">Download comprehensive reports in CSV/Excel.</p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                    <span class="fw-bold text-danger small">Go to Export <i class="fas fa-arrow-right ms-1"></i></span>
                </div>
            </a>
        </div>
    </div>

    <style>
        /* Custom Styles for Dashboard */
        .filter-blur {
            filter: blur(40px);
        }

        .opacity-10 {
            opacity: 0.1 !important;
        }

        .shadow-hover {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
            transition: all 0.3s ease;
        }

        .shadow-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
        }

        .nav-card-link:hover .text-primary {
            color: #0a58ca !important;
        }
    </style>
@endsection