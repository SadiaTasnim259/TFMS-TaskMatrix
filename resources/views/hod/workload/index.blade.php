@extends('hod.layouts.app')

@section('title', 'Staff Workload Overview')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Staff Workload Overview</h1>
            <p class="text-muted">Monitor workload distribution across your department</p>
        </div>
        <div>
        </div>
    </div>

        <!-- Stats Dashboard -->
        <div class="row g-4 mb-5">
            <!-- Total Workload -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-layer-group fa-lg"></i>
                            </div>
                            <div class="dropdown no-arrow">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small fw-bold text-uppercase text-muted mb-1">Total Weightage</div>
                            <div class="h2 mb-0 fw-bold text-gray-800">{{ $stats['total_workload'] }}</div>
                            <div class="small text-muted mt-2">
                                <i class="fas fa-user-friends me-1"></i> Avg:
                                <strong>{{ $stats['average_workload'] }}</strong> / member
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fairness Score -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="avatar bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-balance-scale fa-lg"></i>
                            </div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-info-circle" data-bs-toggle="tooltip"
                                    title="Standard Deviation. Lower is better."></i>
                            </div>
                        </div>
                        <div>
                            <div class="small fw-bold text-uppercase text-muted mb-1">Fairness Score (SD)</div>
                            <div class="h2 mb-0 fw-bold text-gray-800">{{ $stats['fairness_score'] }}</div>
                            <div
                                class="small mt-2 {{ $stats['fairness_score'] < 3 ? 'text-success' : ($stats['fairness_score'] < 6 ? 'text-warning' : 'text-danger') }}">
                                <i
                                    class="fas {{ $stats['fairness_score'] < 3 ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-1"></i>
                                {{ $stats['fairness_score'] < 3 ? 'Excellent Balance' : ($stats['fairness_score'] < 6 ? 'Moderate Variance' : 'High Variance') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribution -->
            <div class="col-xl-6 col-md-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h6 class="m-0 font-weight-bold text-primary">Department Status Distribution</h6>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $total = $stats['staff_count'] > 0 ? $stats['staff_count'] : 1;
                            $underloaded = ($stats['status_counts']['Under-loaded'] ?? 0);
                            $balanced = ($stats['status_counts']['Balanced'] ?? 0);
                            $overloaded = ($stats['status_counts']['Overloaded'] ?? 0);
                         @endphp

                        <div class="progress rounded-pill mb-3" style="height: 30px; font-size: 0.85rem;">
                            @if($underloaded > 0)
                                <div class="progress-bar bg-warning text-dark fw-bold" role="progressbar"
                                    style="width: {{ ($underloaded / $total) * 100 }}%" aria-valuenow="{{ $underloaded }}"
                                    aria-valuemin="0" aria-valuemax="{{ $total }}">
                                    {{ $underloaded }} Under
                                </div>
                            @endif
                            @if($balanced > 0)
                                <div class="progress-bar bg-success fw-bold" role="progressbar"
                                    style="width: {{ ($balanced / $total) * 100 }}%" aria-valuenow="{{ $balanced }}"
                                    aria-valuemin="0" aria-valuemax="{{ $total }}">
                                    {{ $balanced }} Balanced
                                </div>
                            @endif
                            @if($overloaded > 0)
                                <div class="progress-bar bg-danger fw-bold" role="progressbar"
                                    style="width: {{ ($overloaded / $total) * 100 }}%" aria-valuenow="{{ $overloaded }}"
                                    aria-valuemin="0" aria-valuemax="{{ $total }}">
                                    {{ $overloaded }} Over
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-center gap-4 text-xs text-muted">
                            <div class="d-flex align-items-center">
                                <span class="d-inline-block rounded-circle bg-warning me-2"
                                    style="width: 10px; height: 10px;"></span>
                                Under (< {{ $minWeightage }})
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="d-inline-block rounded-circle bg-success me-2"
                                    style="width: 10px; height: 10px;"></span>
                                Balanced ({{ $minWeightage }} - {{ $maxWeightage }})
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="d-inline-block rounded-circle bg-danger me-2"
                                    style="width: 10px; height: 10px;"></span>
                                Over (> {{ $maxWeightage }})
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between border-bottom-0">
                <h6 class="m-0 font-weight-bold text-primary">Department Staff Roster</h6>
                <div class="small text-muted">
                    {{ $stats['staff_count'] }} Staff Members
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Staff Member</th>
                                <th class="py-3 text-center">Active Tasks</th>
                                <th class="py-3 text-center">Total Weightage</th>
                                <th class="py-3">Workload Status</th>
                                <th class="pe-4 py-3 text-end">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departmentStaff as $staff)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px; font-weight: 600;">
                                                {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $staff->first_name }} {{ $staff->last_name }}
                                                </div>
                                                <div class="small text-muted">{{ $staff->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $staff->taskForces->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold h6 mb-0 text-dark">{{ $staff->total_workload }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match ($staff->workload_status) {
                                                'Under-loaded' => 'bg-warning text-dark bg-opacity-25',
                                                'Balanced' => 'bg-success text-white',
                                                'Overloaded' => 'bg-danger text-white',
                                                default => 'bg-secondary',
                                            };
                                            // Specific tweak for warning to look better
                                            if ($staff->workload_status === 'Under-loaded')
                                                $badgeClass = 'bg-warning text-dark';
                                        @endphp
                                        <span
                                            class="badge {{ $badgeClass }} rounded-pill px-3">{{ $staff->workload_status }}</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('hod.workload.show', $staff->id) }}"
                                            class="btn btn-sm btn-light text-primary rounded-circle" data-bs-toggle="tooltip"
                                            title="View Details">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection