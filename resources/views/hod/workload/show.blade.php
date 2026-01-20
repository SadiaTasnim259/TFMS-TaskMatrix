@extends('hod.layouts.app')

@section('title', 'Staff Workload Details')

@section('content')

    <div class="mb-4">
        <a href="{{ route('hod.workload.index') }}" class="text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to Overview
        </a>
    </div>

    <div class="row">
        <!-- Staff Profile & Summary -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                    </div>
                    <h4 class="card-title">{{ $staff->first_name }} {{ $staff->last_name }}</h4>
                    <p class="text-muted mb-1">{{ $staff->email }}</p>
                    <p class="badge bg-secondary">{{ $staff->grade ?? 'N/A' }}</p>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="mb-0">{{ $totalWorkload }}</h5>
                            <small class="text-muted">Total Weightage</small>
                        </div>
                        <div class="col-6">
                            @php
                                $badgeClass = match ($status) {
                                    'Under-loaded' => 'bg-warning text-dark',
                                    'Balanced' => 'bg-success',
                                    'Overloaded' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                            <div class="small text-muted mt-1">Status</div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <!-- Task Force List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Assigned TaskForce</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task Force</th>
                                <th>Role</th>
                                <th>Weightage</th>
                                <th>Assigned</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($staff->taskForces as $tf)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $tf->name }}</div>
                                        <div class="small text-muted">{{ $tf->category }}</div>
                                    </td>
                                    <td>{{ $tf->pivot->role }}</td>
                                    <td>{{ $tf->default_weightage }}</td>
                                    <td>{{ $tf->pivot->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No active TaskForce assigned.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end fw-bold">Total:</td>
                                <td class="fw-bold">{{ $totalWorkload }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection