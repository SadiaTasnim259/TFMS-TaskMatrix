@extends('layouts.app')

@section('title', 'Performance Evaluation Report')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Performance Evaluation Report</h1>
                <p class="text-muted">{{ $academicYear }} Semester {{ $semester }}</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('reports.performance.excel', ['academic_year' => $academicYear, 'semester' => $semester, 'department_id' => request('department_id')]) }}"
                    class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <button class="btn btn-danger" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i> Print
                </button>
                <a href="{{ route('reports.create') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Evaluations</h6>
                        <h2 class="text-primary">{{ $statistics['total_evaluations'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Average Score</h6>
                        <h2 class="text-success">{{ number_format($statistics['average_score'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Highest Score</h6>
                        <h2 class="text-info">{{ number_format($statistics['highest_score'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Lowest Score</h6>
                        <h2 class="text-warning">{{ number_format($statistics['lowest_score'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Rating Distribution</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($ratingDistribution as $rating => $count)
                        @php
                            $percentage = $statistics['total_evaluations'] > 0 ? ($count / $statistics['total_evaluations']) * 100 : 0;
                            $badgeColor = $rating == 'Outstanding' ? 'success' : ($rating == 'Excellent' ? 'primary' : ($rating == 'Good' ? 'info' : ($rating == 'Satisfactory' ? 'warning' : 'danger')));
                        @endphp
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <span class="badge bg-{{ $badgeColor }} mb-2">{{ $rating }}</span>
                                    <h3>{{ $count }}</h3>
                                    <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Score Component Analysis -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Score Component Averages</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6>Teaching Effectiveness</h6>
                        <div class="progress mb-3" style="height: 30px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ ($statistics['avg_teaching_effectiveness'] / 40) * 100 }}%">
                                {{ number_format($statistics['avg_teaching_effectiveness'], 2) }} / 40
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6>Research Contribution</h6>
                        <div class="progress mb-3" style="height: 30px;">
                            <div class="progress-bar bg-info" role="progressbar"
                                style="width: {{ ($statistics['avg_research_contribution'] / 30) * 100 }}%">
                                {{ number_format($statistics['avg_research_contribution'], 2) }} / 30
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6>Service Activities</h6>
                        <div class="progress mb-3" style="height: 30px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ ($statistics['avg_service_activities'] / 30) * 100 }}%">
                                {{ number_format($statistics['avg_service_activities'], 2) }} / 30
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Scores Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-table"></i> Individual Performance Scores</h5>
            </div>
            <div class="card-body">
                @if($scores->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Department</th>
                                    <th>Teaching</th>
                                    <th>Research</th>
                                    <th>Service</th>
                                    <th>Total Score</th>
                                    <th>Rating</th>
                                    <th>Evaluated By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scores as $score)
                                    <tr>
                                        <td>{{ $score->staff->name }}</td>
                                        <td>{{ $score->staff->department->name }}</td>
                                        <td>{{ number_format($score->teaching_effectiveness, 2) }}</td>
                                        <td>{{ number_format($score->research_contribution, 2) }}</td>
                                        <td>{{ number_format($score->service_activities, 2) }}</td>
                                        <td><strong>{{ number_format($score->total_score, 2) }}</strong></td>
                                        <td>
                                            @if($score->rating == 'Outstanding')
                                                <span class="badge bg-success">Outstanding</span>
                                            @elseif($score->rating == 'Excellent')
                                                <span class="badge bg-primary">Excellent</span>
                                            @elseif($score->rating == 'Good')
                                                <span class="badge bg-info">Good</span>
                                            @elseif($score->rating == 'Satisfactory')
                                                <span class="badge bg-warning">Satisfactory</span>
                                            @else
                                                <span class="badge bg-danger">Needs Improvement</span>
                                            @endif
                                        </td>
                                        <td>{{ $score->evaluatedBy->name ?? 'N/A' }}</td>
                                        <td>{{ $score->evaluation_date->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">Average</th>
                                    <th>{{ number_format($statistics['avg_teaching_effectiveness'], 2) }}</th>
                                    <th>{{ number_format($statistics['avg_research_contribution'], 2) }}</th>
                                    <th>{{ number_format($statistics['avg_service_activities'], 2) }}</th>
                                    <th>{{ number_format($statistics['average_score'], 2) }}</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $scores->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No performance evaluations found for this period</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection