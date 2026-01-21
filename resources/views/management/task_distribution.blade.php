@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 section-title mb-0">TaskForce Distribution Overview</h1>
                <p class="text-muted small mb-0">
                    Distribution of active TaskForce across departments.
                    @if(isset($currentSession))
                        <span class="badge bg-primary ms-2">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $currentSession->academic_year }} - Semester {{ $currentSession->semester }}
                        </span>
                    @endif
                </p>
            </div>
            <div class="badge bg-primary fs-6 shadow-sm">
                Total Active TaskForce: {{ $totalTaskForces }}
            </div>
        </div>

        <div class="row">
            <!-- Chart Section -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header py-3 bg-transparent border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Distribution Chart</h6>
                    </div>
                    <div class="card-body">
                        <div style="height: 400px;">
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Table -->
            <div class="col-lg-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header py-3 bg-transparent border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Detailed Breakdown</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3 border-0">Department</th>
                                        <th class="px-4 py-3 border-0 text-center fw-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($distributionData as $data)
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-dark">{{ $data['name'] }}</td>
                                            <td class="px-4 py-3 text-center fw-bold">{{ $data['total'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('distributionChart').getContext('2d');

            const departments = @json(array_column($distributionData, 'name'));
            const rawData = @json($distributionData);
            const categories = @json($categories);

            // Prepare Datasets for Stacked Bar
            // We need an array of datasets, one for each category.
            // Each dataset needs an array of data points corresponding to the departments.

            const colors = [
                '#0d6efd', // Primary Blue
                '#6610f2', // Purple
                '#198754', // Success Green
                '#fd7e14', // Orange
                '#ffc107', // Warning Yellow
                '#0dcaf0', // Info Cyan
            ];

            const datasets = categories.map((category, index) => {
                return {
                    label: category,
                    data: rawData.map(dept => dept.stats[category]),
                    backgroundColor: colors[index % colors.length],
                    stack: 'Stack 0',
                };
            });

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: departments,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection