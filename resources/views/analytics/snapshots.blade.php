@extends('layouts.app')

@section('title', 'Analytics Snapshots')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Analytics Snapshots</h1>
                <p class="text-muted">Historical analytics data snapshots</p>
                <nav aria-label="breadcrumb">

                </nav>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('analytics.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>



        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filters
            </div>
            <div class="card-body">
                <form action="{{ route('analytics.snapshots') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Created By</label>
                            <select name="created_by" class="form-select">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Snapshots List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-camera"></i> Snapshots History</h5>
            </div>
            <div class="card-body">
                @if($snapshots->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Snapshot Date</th>
                                    <th>Created By</th>
                                    <th>Total Workload Hours</th>
                                    <th>Active Staff</th>
                                    <th>Pending Submissions</th>
                                    <th>Avg Performance</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($snapshots as $snapshot)
                                    <tr>
                                        <td>
                                            <strong>{{ $snapshot->snapshot_date->format('M d, Y H:i') }}</strong>
                                        </td>
                                        <td>{{ $snapshot->createdBy->name ?? 'System' }}</td>
                                        <td>{{ number_format($snapshot->total_workload_hours, 2) }}</td>
                                        <td>{{ $snapshot->active_staff_count }}</td>
                                        <td>
                                            <span class="badge bg-warning">{{ $snapshot->pending_submissions }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-success">{{ number_format($snapshot->avg_performance_score, 2) }}</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#snapshotModal{{ $snapshot->id }}">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $snapshots->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No snapshots found</p>
                        <a href="{{ route('analytics.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Go to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Snapshot Detail Modals -->
    @foreach($snapshots as $snapshot)
        <div class="modal fade" id="snapshotModal{{ $snapshot->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-camera"></i> Snapshot Details -
                            {{ $snapshot->snapshot_date->format('M d, Y H:i') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted">Total Workload Hours</h6>
                                        <h2 class="text-primary">{{ number_format($snapshot->total_workload_hours, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted">Active Staff Count</h6>
                                        <h2 class="text-success">{{ $snapshot->active_staff_count }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted">Pending Submissions</h6>
                                        <h2 class="text-warning">{{ $snapshot->pending_submissions }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted">Average Performance Score</h6>
                                        <h2 class="text-info">{{ number_format($snapshot->avg_performance_score, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($snapshot->metadata)
                            <div class="mt-4">
                                <h6>Additional Metadata</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre
                                        class="mb-0"><code>{{ json_encode(json_decode($snapshot->metadata), JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        @endif

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-user"></i> Created by {{ $snapshot->createdBy->name ?? 'System' }}
                                on {{ $snapshot->created_at->format('M d, Y H:i:s') }}
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection