@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <span class="badge badge-soft mb-2">System Administrator</span>
            <h1 class="h2 section-title">Operational Control Centre</h1>
            <p class="text-muted">Keep master data clean, enforce rules, and safeguard auditability.</p>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50 fw-bold text-uppercase">Current User</small>
                            <h5 class="mb-0 fw-bold">{{ Auth::user()->name }}</h5>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-user-shield fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="row g-4 mb-5">


        <!-- Departments -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Departments</h5>
                    <p class="card-text text-muted small">Configure academic departments and organizational units.</p>
                    <a href="{{ route('admin.departments.index') }}"
                        class="btn btn-outline-success btn-sm stretched-link">Manage Depts</a>
                </div>
            </div>
        </div>

        <!-- User Accounts -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-user-lock fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">User Accounts</h5>
                    <p class="card-text text-muted small">Handle logins, password resets, and account security.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-warning btn-sm stretched-link">Manage
                        Users</a>
                </div>
            </div>
        </div>

        <!-- System Config -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-secondary bg-opacity-10 text-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-cogs fa-2x"></i>
                    </div>
                    <h5 class="card-title fw-bold">Workload Thresholds</h5>
                    <p class="card-text text-muted small">Manage minimum and maximum workload limits.</p>
                    <a href="{{ route('admin.workload.thresholds.edit') }}"
                        class="btn btn-outline-secondary btn-sm stretched-link">Manage Thresholds</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Stats -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-history me-2"></i>Recent Audit Logs</h5>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">User</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th class="text-end pe-4">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs as $log)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                @if($log->user)
                                                    <div class="avatar-xs bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 32px; height: 32px; font-size: 12px;">
                                                        {{ substr($log->user->name, 0, 2) }}
                                                    </div>
                                                    <span class="fw-semibold">{{ $log->user->name }}</span>
                                                @else
                                                    <div class="avatar-xs bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 32px; height: 32px; font-size: 12px;">SY</div>
                                                    <span class="fw-semibold">System</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match ($log->action) {
                                                    'CREATE' => 'success',
                                                    'UPDATE' => 'warning',
                                                    'DELETE' => 'danger',
                                                    'LOGIN', 'LOGOUT' => 'info',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span
                                                class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }}">{{ $log->action }}</span>
                                            <small class="text-muted ms-1">{{ Str::limit($log->description, 20) }}</small>
                                        </td>
                                        <td>{{ class_basename($log->model_type) }}</td>
                                        <td class="text-end pe-4 text-muted small">{{ $log->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No recent activity found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-chart-pie me-2"></i>System Health</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Storage Usage</span>
                            <span class="text-muted small">{{ $storageUsage }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $storageUsage }}%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Database Size (Quota)</span>
                            <span class="text-muted small">{{ $dbLoad }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $dbLoad }}%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Memory Usage (App)</span>
                            <span class="text-muted small">{{ $memoryUsage }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $memoryUsage }}%">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-grid">
                        <a href="{{ route('admin.system-report') }}" class="btn btn-light text-primary fw-bold">
                            <i class="fas fa-download me-2"></i>Download System Report
                        </a>
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