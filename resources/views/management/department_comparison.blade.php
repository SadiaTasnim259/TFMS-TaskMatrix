@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 section-title mb-0">Department Comparison Overview</h1>
                <p class="text-muted small mb-0">Compare workload metrics and staff distribution across all departments.</p>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Average Workload Chart -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header py-3 bg-transparent border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Average Workload by Department</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="avgWorkloadChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Distribution Chart -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header py-3 bg-transparent border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Workload Status Distribution</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="statusDistChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Comparison Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header py-3 bg-transparent border-bottom-0">
                <h6 class="m-0 fw-bold text-primary">Detailed Stats</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 border-0">Department</th>
                                <th class="px-4 py-3 border-0 text-center">Staff Count</th>
                                <th class="px-4 py-3 border-0 text-center">Avg Weightage</th>
                                <th class="px-4 py-3 border-0">Workload Status Breakdown</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparisonData as $dept)
                                <tr>
                                    <td class="px-4 py-3 fw-bold text-dark">{{ $dept['name'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $dept['staff_count'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-light text-dark border">{{ $dept['avg_weightage'] }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="progress" style="height: 6px;">
                                            @php
                                                $total = array_sum($dept['status_distribution']);
                                                $under = $total > 0 ? ($dept['status_distribution']['Under-loaded'] / $total) * 100 : 0;
                                                $balanced = $total > 0 ? ($dept['status_distribution']['Balanced'] / $total) * 100 : 0;
                                                $over = $total > 0 ? ($dept['status_distribution']['Overloaded'] / $total) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $under }}%"
                                                title="Under-loaded: {{ $dept['status_distribution']['Under-loaded'] }}"></div>
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $balanced }}%"
                                                title="Balanced: {{ $dept['status_distribution']['Balanced'] }}"></div>
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $over }}%"
                                                title="Overloaded: {{ $dept['status_distribution']['Overloaded'] }}"></div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-1"
                                            style="font-size: 10px; color: #6c757d;">
                                            <span>Under: {{ $dept['status_distribution']['Under-loaded'] }}</span>
                                            <span>Bal: {{ $dept['status_distribution']['Balanced'] }}</span>
                                            <span>Over: {{ $dept['status_distribution']['Overloaded'] }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = @json($comparisonData);
            const labels = data.map(d => d.name);

            // Chart 1: Average Workload
            const avgCtx = document.getElementById('avgWorkloadChart').getContext('2d');
            new Chart(avgCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Average Weightage',
                        data: data.map(d => d.avg_weightage),
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Chart 2: Status Distribution (Stacked)
            const statusCtx = document.getElementById('statusDistChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Under-loaded',
                            data: data.map(d => d.status_distribution['Under-loaded']),
                            backgroundColor: '#ffc107',
                        },
                        {
                            label: 'Balanced',
                            data: data.map(d => d.status_distribution['Balanced']),
                            backgroundColor: '#198754',
                        },
                        {
                            label: 'Overloaded',
                            data: data.map(d => d.status_distribution['Overloaded']),
                            backgroundColor: '#dc3545',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { stacked: true },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
    </script>
@endsection