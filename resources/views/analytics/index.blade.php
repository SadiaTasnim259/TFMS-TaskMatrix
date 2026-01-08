@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Create Snapshot Modal -->
        <div class="modal fade" id="createSnapshotModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Analytics Snapshot</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to create a new analytics snapshot?</p>
                        <p class="text-muted small">This will record the current state of workload distribution and
                            performance metrics for historical tracking.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('analytics.snapshot') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-camera me-2"></i> Create Snapshot
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Analytics Dashboard</h1>
                <p class="text-muted">Real-time workload and performance insights</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('analytics.snapshots') }}" class="btn btn-outline-primary">
                    <i class="fas fa-history"></i> View Snapshots
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSnapshotModal">
                    <i class="fas fa-camera"></i> Create Snapshot
                </button>
            </div>
        </div>



        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-0">Total Workload Hours</h6>
                                <h2 class="mt-2">{{ number_format($metrics['total_hours'], 0) }}</h2>
                            </div>
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-0">Active Staff</h6>
                                <h2 class="mt-2">{{ $metrics['active_staff'] }}</h2>
                            </div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-0">Pending Approvals</h6>
                                <h2 class="mt-2">{{ $metrics['pending_submissions'] }}</h2>
                            </div>
                            <i class="fas fa-hourglass-half fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-0">Avg Performance</h6>
                                <h2 class="mt-2">{{ number_format($metrics['avg_performance_score'], 1) }}</h2>
                            </div>
                            <i class="fas fa-chart-line fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Workload Distribution Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Workload by Activity Type</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="workloadChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Department Comparison Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Department Workload Comparison</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="departmentChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Trends -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Performance Trends</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceTrendChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Stats Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-table"></i> Department Statistics</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Department</th>
                                <th>Staff Count</th>
                                <th>Total Hours</th>
                                <th>Avg Hours/Staff</th>
                                <th>Pending</th>
                                <th>Approved</th>
                                <th>Approval Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departmentStats as $stat)
                                <tr>
                                    <td>{{ $stat['department_name'] }}</td>
                                    <td>{{ $stat['staff_count'] }}</td>
                                    <td>{{ number_format($stat['total_hours'], 2) }}</td>
                                    <td>{{ number_format($stat['avg_hours_per_staff'], 2) }}</td>
                                    <td><span class="badge bg-warning">{{ $stat['pending_count'] }}</span></td>
                                    <td><span class="badge bg-success">{{ $stat['approved_count'] }}</span></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $stat['approval_rate'] }}%">
                                                {{ number_format($stat['approval_rate'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($recentActivity as $activity)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                    <strong>{{ $activity['action'] }}</strong> - {{ $activity['description'] }}
                                </div>
                                <small class="text-muted">{{ $activity['timestamp'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            // Workload Distribution Pie Chart
            const workloadCtx = document.getElementById('workloadChart');
            new Chart(workloadCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode(array_keys($workloadByType)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($workloadByType)) !!},
                        backgroundColor: [
                            '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Department Bar Chart
            const departmentCtx = document.getElementById('departmentChart');
            new Chart(departmentCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($departmentStats, 'department_name')) !!},
                    datasets: [{
                        label: 'Total Hours',
                        data: {!! json_encode(array_column($departmentStats, 'total_hours')) !!},
                        backgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Performance Trend Line Chart
            const performanceCtx = document.getElementById('performanceTrendChart');
            new Chart(performanceCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($performanceTrend['labels']) !!},
                    datasets: [{
                        label: 'Average Performance Score',
                        data: {!! json_encode($performanceTrend['data']) !!},
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection