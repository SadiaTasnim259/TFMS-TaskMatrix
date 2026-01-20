@extends('layouts.app')

@section('title', 'Assigned Task Forces')

@section('content')
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Your Task Force Assignments</h1>
            <p class="text-muted small mb-0">
                Task forces you are currently assigned to for this session.
                @if(isset($currentSession))
                    <span class="badge bg-primary ms-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $currentSession->academic_year }} - Semester {{ $currentSession->semester }}
                    </span>
                @endif
            </p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Assignments Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-briefcase me-2"></i>Current Assignments
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Task Force Name</th>
                            <th>Your Role</th>
                            <th>Weightage</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taskForces as $tf)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $tf->name }}</div>
                                    <small class="text-muted">{{ $tf->academic_year }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3">
                                        {{ $tf->pivot->role ?? 'Member' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $tf->default_weightage ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($tf->is_locked)
                                        <span class="badge bg-warning text-dark"><i class="fas fa-lock me-1"></i> Locked</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    {{-- Placeholder for future details view --}}
                                    <button class="btn btn-sm btn-light text-muted" disabled title="View Details (Coming Soon)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-clipboard-check fa-3x text-muted opacity-25"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal">You are not currently assigned to any taskforce.</h5>
                                    <p class="text-muted small mb-0">Assignments for the current session will appear here once
                                        finalized.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection