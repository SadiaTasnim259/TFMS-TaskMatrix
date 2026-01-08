@extends('layouts.app')

@section('title', 'Department Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 section-title mb-0">Department: {{ $department->name }}</h1>
            <a href="{{ route('management.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">Staff Workload Details</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="px-4 py-3 border-0">Staff Name</th>
                                <th scope="col" class="px-4 py-3 border-0">Total Weightage</th>
                                <th scope="col" class="px-4 py-3 border-0">Status</th>
                                <th scope="col" class="px-4 py-3 border-0">Task Forces</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffStats as $stat)
                                <tr>
                                    <td class="px-4 py-3 fw-bold text-dark">
                                        {{ $stat['staff']->first_name }} {{ $stat['staff']->last_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="fw-bold">{{ $stat['total_weightage'] }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusClass = match ($stat['status']) {
                                                'Overloaded' => 'bg-danger',
                                                'Balanced' => 'bg-success',
                                                'Under-loaded' => 'bg-warning text-dark',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ $stat['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($stat['staff']->taskForces->isNotEmpty())
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach($stat['staff']->taskForces as $tf)
                                                    <li class="mb-1">
                                                        <i class="fas fa-check-circle text-primary me-1"></i>
                                                        {{ $tf->name }}
                                                        <span class="text-muted">({{ $tf->default_weightage }})</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted fst-italic">No task forces assigned</span>
                                        @endif
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