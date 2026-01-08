@extends('layouts.app')

@section('title', 'Performance Evaluation Details')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Performance Evaluation Details</h1>
                <nav aria-label="breadcrumb">

                </nav>
            </div>
            <div class="col-md-4 text-end">
                @if(auth()->user()->isAdmin() || auth()->user()->isHOD())
                    <a href="{{ route('performance.edit', $score) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>



        <div class="row">
            <!-- Left Column: Details -->
            <div class="col-md-8">
                <!-- Staff Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Staff Information</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluation Period -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar"></i> Evaluation Period</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Academic Year:</strong><br>
                        <span class="fs-5">{{ $score->academic_year }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Semester:</strong><br>
                        <span class="fs-5">Semester {{ $score->semester }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Evaluation Date:</strong><br>
                        <span class="fs-5">{{ $score->evaluation_date->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Scores Breakdown -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Performance Scores</h5>
            </div>
            <div class="card-body">
                <!-- Teaching Effectiveness -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Teaching Effectiveness</strong>
                        <span>{{ number_format($score->teaching_effectiveness, 2) }} / 40</span>
                    </div>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-primary" role="progressbar"
                            style="width: {{ ($score->teaching_effectiveness / 40) * 100 }}%">
                            {{ number_format(($score->teaching_effectiveness / 40) * 100, 1) }}%
                        </div>
                    </div>
                    <small class="text-muted">Course delivery, student engagement, assessment quality</small>
                </div>

                <!-- Research Contribution -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Research Contribution</strong>
                        <span>{{ number_format($score->research_contribution, 2) }} / 30</span>
                    </div>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-info" role="progressbar"
                            style="width: {{ ($score->research_contribution / 30) * 100 }}%">
                            {{ number_format(($score->research_contribution / 30) * 100, 1) }}%
                        </div>
                    </div>
                    <small class="text-muted">Publications, grants, research projects, supervision</small>
                </div>

                <!-- Service Activities -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Service Activities</strong>
                        <span>{{ number_format($score->service_activities, 2) }} / 30</span>
                    </div>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ ($score->service_activities / 30) * 100 }}%">
                            {{ number_format(($score->service_activities / 30) * 100, 1) }}%
                        </div>
                    </div>
                    <small class="text-muted">Committee work, administrative tasks, community service</small>
                </div>
            </div>
        </div>

        <!-- Comments -->
        @if($score->comments)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Evaluator Comments</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $score->comments }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Summary -->
    <div class="col-md-4">
        <!-- Overall Score Card -->
        <div class="card mb-4 text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Score</h6>
                <h1 class="display-1 text-primary mb-0">{{ number_format($score->total_score, 1) }}</h1>
                <p class="text-muted">out of 100</p>
                <hr>
                <h5>Overall Rating</h5>
                @if($score->rating == 'Outstanding')
                    <span class="badge bg-success fs-4">Outstanding</span>
                @elseif($score->rating == 'Excellent')
                    <span class="badge bg-primary fs-4">Excellent</span>
                @elseif($score->rating == 'Good')
                    <span class="badge bg-info fs-4">Good</span>
                @elseif($score->rating == 'Satisfactory')
                    <span class="badge bg-warning fs-4">Satisfactory</span>
                @else
                    <span class="badge bg-danger fs-4">Needs Improvement</span>
                @endif
            </div>
        </div>

        <!-- Component Scores -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Score Breakdown</h5>
            </div>
            <div class="card-body">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <span>Teaching</span>
                        <strong class="text-primary">{{ number_format($score->teaching_effectiveness, 2) }}</strong>
                    </div>
                    <small class="text-muted">{{ number_format(($score->teaching_effectiveness / 40) * 100, 1) }}%
                        of max</small>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <span>Research</span>
                        <strong class="text-info">{{ number_format($score->research_contribution, 2) }}</strong>
                    </div>
                    <small class="text-muted">{{ number_format(($score->research_contribution / 30) * 100, 1) }}% of
                        max</small>
                </div>
                <div>
                    <div class="d-flex justify-content-between">
                        <span>Service</span>
                        <strong class="text-success">{{ number_format($score->service_activities, 2) }}</strong>
                    </div>
                    <small class="text-muted">{{ number_format(($score->service_activities / 30) * 100, 1) }}% of
                        max</small>
                </div>
            </div>
        </div>

        <!-- Evaluation Metadata -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Evaluation Info</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Evaluated By:</strong><br>
                    {{ $score->evaluatedBy->name ?? 'N/A' }}
                </div>
                <div class="mb-3">
                    <strong>Evaluation Date:</strong><br>
                    {{ $score->evaluation_date->format('M d, Y') }}
                </div>
                <div class="mb-3">
                    <strong>Created:</strong><br>
                    {{ $score->created_at->format('M d, Y H:i') }}
                </div>
                <div>
                    <strong>Last Updated:</strong><br>
                    {{ $score->updated_at->format('M d, Y H:i') }}
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection