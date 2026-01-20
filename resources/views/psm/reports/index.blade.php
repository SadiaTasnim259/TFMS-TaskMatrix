@extends('admin.layouts.app')

@section('title', 'Generate Faculty Reports')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Generate Faculty Reports</h1>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">Report Configuration</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('psm.reports.generate') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="report_type" class="form-label fw-bold">Select Report Type</label>
                                <select class="form-select form-select-lg" name="report_type" id="report_type" required>
                                    <option value="" disabled selected>-- Choose Report --</option>
                                    <option value="taskforce_list">📋 Task Force List & Members</option>
                                    <option value="workload_summary">📈 Workload Summaries</option>
                                    <option value="overload_underload">⚖️ Overload / Underload Report</option>
                                </select>
                                <div class="form-text mt-2" id="report_desc">
                                    Select a report type to see details.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Output Format</label>
                                <div class="d-flex gap-3">
                                    <div
                                        class="border rounded p-3 flex-fill hover-bg-light position-relative d-flex align-items-center">
                                        <div class="form-check w-100 mb-0">
                                            <input class="form-check-input" type="radio" name="format" id="format_pdf"
                                                value="pdf" checked>
                                            <label class="form-check-label stretched-link d-flex align-items-center"
                                                for="format_pdf">
                                                <i class="fas fa-file-pdf text-danger fs-4 me-3"></i>
                                                <span class="fw-bold text-dark">PDF Document</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div
                                        class="border rounded p-3 flex-fill hover-bg-light position-relative d-flex align-items-center">
                                        <div class="form-check w-100 mb-0">
                                            <input class="form-check-input" type="radio" name="format" id="format_excel"
                                                value="excel">
                                            <label class="form-check-label stretched-link d-flex align-items-center"
                                                for="format_excel">
                                                <i class="fas fa-file-csv text-success fs-4 me-3"></i>
                                                <span class="fw-bold text-dark">CSV</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-download me-2"></i> Generate Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-info">Report Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark">📋 Task Force List & Members</h6>
                            <p class="small text-muted mb-0">Full list of all TaskForce, their leaders, assigned
                                departments, and member statistics.</p>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark">📈 Workload Summaries</h6>
                            <p class="small text-muted mb-0">Detailed breakdown of workload distribution across all
                                departments.</p>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <h6 class="fw-bold text-dark">⚖️ Overload / Underload Report</h6>
                            <p class="small text-muted mb-0">Highlights staff members who are significantly above or below
                                the target workload range.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fc;
            cursor: pointer;
        }
    </style>
@endsection