@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 section-title mb-0">Executive Dashboard</h1>
                @if(isset($currentSession))
                    <span class="badge bg-primary mt-1">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $currentSession->academic_year }} - Semester {{ $currentSession->semester }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Top Stats Cards -->
        <div class="row g-4 mb-4">
            <!-- Under-loaded -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-start border-4 border-warning h-100 placeholder-wave">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Under-loaded Staff</div>
                        <div class="h3 mb-0 fw-bold text-dark">{{ $fairnessStats['Under-loaded'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Balanced -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Balanced Staff</div>
                        <div class="h3 mb-0 fw-bold text-dark">{{ $fairnessStats['Balanced'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Overloaded -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overloaded Staff</div>
                        <div class="h3 mb-0 fw-bold text-dark">{{ $fairnessStats['Overloaded'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Workload Fairness Chart -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3 bg-transparent border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Workload Fairness Distribution</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="fairnessChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Department Comparison Table -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3 bg-transparent border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Department Workload Comparison</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 border-0">Department</th>
                                        <th scope="col" class="px-4 py-3 border-0">Avg Weightage</th>
                                        <th scope="col" class="px-4 py-3 border-0">Staff Count</th>
                                        <th scope="col" class="px-4 py-3 border-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departmentStats as $deptId => $stat)
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-dark">{{ $stat['name'] }}</td>
                                            <td class="px-4 py-3">{{ $stat['average_weightage'] }}</td>
                                            <td class="px-4 py-3">{{ $stat['staff_count'] }}</td>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('management.department', $deptId) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </td>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Fairness Chart
        const fairnessCtx = document.getElementById('fairnessChart').getContext('2d');
        new Chart(fairnessCtx, {
            type: 'bar',
            data: {
                labels: ['Under-loaded', 'Balanced', 'Overloaded'],
                datasets: [{
                    label: 'Staff Count',
                    data: [{{ $fairnessStats['Under-loaded'] }}, {{ $fairnessStats['Balanced'] }}, {{ $fairnessStats['Overloaded'] }}],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.5)',
                        'rgba(25, 135, 84, 0.5)',
                        'rgba(220, 53, 69, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });


    </script>
@endsection