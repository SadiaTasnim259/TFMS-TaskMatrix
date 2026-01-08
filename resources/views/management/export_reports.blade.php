@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 section-title mb-0">Export Summary Reports</h1>
                <p class="text-muted small mb-0">Generate and download system-wide reports.</p>
            </div>
        </div>

        <div class="row">
            <!-- Export Configuration Card -->
            <div class="col-md-8 col-lg-6 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="m-0 fw-bold text-primary"><i class="fas fa-file-export me-2"></i>Generate Report</h5>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('management.generate_report') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="report_type" class="form-label fw-bold small text-uppercase text-muted">Select
                                    Report Type</label>
                                <select class="form-select @error('report_type') is-invalid @enderror" id="report_type"
                                    name="report_type" required>
                                    <option value="" selected disabled>Choose a report type...</option>
                                    <option value="workload" {{ old('report_type') == 'workload' ? 'selected' : '' }}>
                                        📋 Workload Summary (All Staff)
                                    </option>
                                    <option value="department" {{ old('report_type') == 'department' ? 'selected' : '' }}>
                                        🏢 Department Comparison (Aggregated Stats)
                                    </option>
                                    <option value="taskforce" {{ old('report_type') == 'taskforce' ? 'selected' : '' }}>
                                        📊 Taskforce Distribution
                                    </option>
                                </select>
                                @error('report_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Choose the dataset you want to export.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Select Format</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check custom-option basic">
                                        <input class="form-check-input" type="radio" name="format" id="format_csv"
                                            value="csv" checked>
                                        <label class="form-check-label" for="format_csv">
                                            <span class="d-block fw-bold"><i class="fas fa-file-csv me-1 text-success"></i>
                                                CSV / Excel</span>
                                            <small class="text-muted">Best for data analysis</small>
                                        </label>
                                    </div>
                                    <div class="form-check custom-option basic">
                                        <input class="form-check-input" type="radio" name="format" id="format_pdf"
                                            value="pdf">
                                        <label class="form-check-label" for="format_pdf">
                                            <span class="d-block fw-bold"><i class="fas fa-file-pdf me-1 text-danger"></i>
                                                PDF</span>
                                            <small class="text-muted">Best for printing (Coming Soon)</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="fas fa-download me-2"></i> Download Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Radio Selection Styling */
        .custom-option {
            border: 1px solid #ced4da;
            border-radius: 0.5rem;
            padding: 1rem;
            padding-left: 2.5rem;
            /* Make room for the radio circle */
            cursor: pointer;
            flex: 1;
            transition: all 0.2s ease;
            position: relative;
            /* Context for nested absolute pos if needed */
        }

        /* Ensure the native radio button is positioned correctly within the padded area */
        .custom-option .form-check-input {
            margin-left: -1.5rem;
            /* Pull it back into the padding area */
            margin-top: 0.3rem;
        }

        .custom-option:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }

        .custom-option .form-check-input:checked~.form-check-label {
            color: #0d6efd;
        }

        /* When input is checked, style the parent div? CSS :has() is modern but let's stick to safe CSS or simple styles */
        /* We can't style the parent based on child easily without :has. 
       So just styling the label/input relation is fine. 
    */
    </style>
@endsection