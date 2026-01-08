@extends('layouts.app')

@section('title', 'Historical Records')

@section('content')
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Historical Records</h1>
            <p class="text-muted small mb-0">View your past task force assignments and workload summaries.</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 bg-light rounded">
            <form action="{{ route('workload.history') }}" method="GET" class="row align-items-end">
                <div class="col-md-4">
                    <label for="year" class="form-label fw-bold">Select Academic Session</label>
                    <select name="year" id="year" class="form-select">
                        <option value="" disabled {{ !$selectedYear ? 'selected' : '' }}>Choose a session...</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> View Records
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedYear)
        <div class="row">
            <!-- Status Card -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h6 class="text-uppercase text-muted fw-bold mb-3">Status for {{ $selectedYear }}</h6>

                        <div class="mb-3">
                            <span
                                class="display-6 fw-bold {{ str_contains($statusColor, 'green') ? 'text-success' : (str_contains($statusColor, 'yellow') ? 'text-warning' : 'text-danger') }}">
                                {{ $status }}
                            </span>
                        </div>

                        <div class="py-3">
                            <h2 class="display-3 fw-bold text-dark">{{ $totalWorkload }}</h2>
                            <p class="text-muted">Total Hours / Weightage</p>
                        </div>

                        <!-- Progress Bar -->
                        @php
                            $percentage = ($totalWorkload / ($maxWeightage * 1.5)) * 100;
                            if ($percentage > 100)
                                $percentage = 100;

                            $barColor = match ($status) {
                                'Under-loaded' => 'bg-warning',
                                'Balanced' => 'bg-success',
                                'Overloaded' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $percentage }}%"
                                aria-valuenow="{{ $totalWorkload }}" aria-valuemin="0" aria-valuemax="30"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1 text-xs text-muted">
                            <span>0</span>
                            <span>Min: {{ $minWeightage }}</span>
                            <span>Max: {{ $maxWeightage }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Breakdown Table -->
            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-history me-2"></i>Assignments in {{ $selectedYear }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Task Force</th>
                                        <th>Role</th>
                                        <th class="text-end pe-4">Weightage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($taskForces as $tf)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold">{{ $tf->name }}</div>
                                                <small class="text-muted">{{ $tf->category }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ $tf->pivot->role ?? 'Member' }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="fw-bold">{{ $tf->default_weightage }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <p class="text-muted mb-0">No records found for this session.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted opacity-25 mb-3"></i>
            <h5 class="text-muted">Select an academic session above to view historical records.</h5>
        </div>
    @endif

@endsection