@extends('layouts.app')

@section('title', 'Audit Log Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 section-title mb-0">Audit Log Details</h1>
                <p class="text-muted mt-1">View detailed information about this system activity</p>
            </div>
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Logs
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-dark fw-bold">Log Information #{{ $log->id }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <tbody>
                            <!-- Action -->
                            <tr>
                                <th class="ps-4 py-3" style="width: 25%;">Action</th>
                                <td class="pe-4 py-3">
                                    @php
                                        $badgeClass = match ($log->action) {
                                            'create' => 'bg-success',
                                            'update' => 'bg-info text-dark',
                                            'delete' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ strtoupper($log->action) }}</span>
                                </td>
                            </tr>

                            <!-- User -->
                            <tr>
                                <th class="ps-4 py-3">User</th>
                                <td class="pe-4 py-3">
                                    @if($log->user)
                                        <div>
                                            <div class="fw-bold text-dark">{{ $log->user->name }}</div>
                                            <small class="text-muted">{{ $log->user->email }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">System / Deleted User</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Description -->
                            <tr>
                                <th class="ps-4 py-3">Description</th>
                                <td class="pe-4 py-3">{{ $log->description }}</td>
                            </tr>

                            <!-- Status -->
                            <tr>
                                <th class="ps-4 py-3">Status</th>
                                <td class="pe-4 py-3">
                                    @if($log->status === 'completed' || $log->status === 'SUCCESS')
                                        <span class="badge bg-success">Success</span>
                                    @elseif($log->status === 'failed' || $log->status === 'FAILED')
                                        <span class="badge bg-danger">Failed</span>
                                    @elseif($log->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Model Type -->
                            <tr>
                                <th class="ps-4 py-3">Model Type</th>
                                <td class="pe-4 py-3">
                                    <code>{{ class_basename($log->model_type) }}</code>
                                    <span class="text-muted ms-2">(ID: {{ $log->model_id }})</span>
                                </td>
                            </tr>

                            <!-- IP Address -->
                            <tr>
                                <th class="ps-4 py-3">IP Address</th>
                                <td class="pe-4 py-3 font-monospace">{{ $log->ip_address ?? 'N/A' }}</td>
                            </tr>

                            <!-- User Agent -->
                            <tr>
                                <th class="ps-4 py-3">User Agent</th>
                                <td class="pe-4 py-3 text-break small">{{ $log->user_agent ?? 'N/A' }}</td>
                            </tr>

                            <!-- Timestamp -->
                            <tr>
                                <th class="ps-4 py-3">Timestamp</th>
                                <td class="pe-4 py-3">
                                    {{ $log->created_at->format('d M Y, h:i A') }}
                                    <small class="text-muted ms-2">({{ $log->created_at->diffForHumans() }})</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection