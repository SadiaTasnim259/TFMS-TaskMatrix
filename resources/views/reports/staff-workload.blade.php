@extends('layouts.app')

@section('title', 'Staff Workload Report')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Staff Workload Report</h1>
                <p class="text-muted">{{ $staff->name }} - {{ $report->academic_year }} Semester {{ $report->semester }}</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('reports.show', ['report' => $report->id, 'format' => 'pdf']) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>

                {{-- Excel Export Form --}}
                <form action="{{ route('reports.export.workload-excel') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="academic_year" value="{{ $report->academic_year }}">
                    <input type="hidden" name="semester" value="{{ $report->semester }}">
                    <input type="hidden" name="staff_id" value="{{ $staff->id }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                </form>
                <a href="{{ route('reports.create') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Staff Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user"></i> Staff Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Name:</strong><br>
                        {{ $staff->name }}
                    </div>
                    <div class="col-md-3">
                        <strong>Staff ID:</strong><br>
                        {{ $staff->employee_id ?? $staff->id }}
                    </div>
                    <div class="col-md-3">
                        <strong>Department:</strong><br>
                        {{ $staff->department->name }}
                    </div>
                    <div class="col-md-3">
                        <strong>Position:</strong><br>
                        {{ $staff->position }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6>Total Submissions</h6>
                        <h2>{{ $summary['total_submissions'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6>Total Hours</h6>
                        <h2>{{ number_format($summary['total_hours'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6>Total Credits</h6>
                        <h2>{{ number_format($summary['total_credits'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6>Total Activities</h6>
                        <h2>{{ $summary['total_activities'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workload Submissions -->
        @foreach($submissions as $submission)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt"></i> Submission #{{ $submission->id }}
                    </h5>
                    <span
                        class="badge bg-{{ $submission->status == 'approved' ? 'success' : ($submission->status == 'submitted' ? 'warning' : ($submission->status == 'rejected' ? 'danger' : 'secondary')) }}">
                        {{ ucfirst($submission->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Submitted:</strong>
                            {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Total Hours:</strong> {{ number_format($submission->total_hours, 2) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Total Credits:</strong> {{ number_format($submission->total_credits, 2) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Activities:</strong> {{ $submission->items->count() }}
                        </div>
                    </div>

                    @if($submission->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Activity Name</th>
                                        <th>Hours</th>
                                        <th>Credits</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submission->items as $item)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $item->getActivityTypeColor() }}">
                                                    {{ $item->getActivityTypeLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ $item->activity_name }}</td>
                                            <td>{{ number_format($item->hours_allocated, 2) }}</td>
                                            <td>{{ number_format($item->credits_value, 2) }}</td>
                                            <td><small class="text-muted">{{ $item->description ?: '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($submission->notes)
                        <div class="alert alert-info mt-3">
                            <strong>Notes:</strong> {{ $submission->notes }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($submissions->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No workload submissions found for this period</p>
                </div>
            </div>
        @endif
    </div>
@endsection