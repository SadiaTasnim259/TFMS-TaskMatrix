@extends('admin.layouts.app')

@section('title', 'Performance Evaluation Weights')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark">Performance Evaluation Weights</h1>
            <a href="{{ route('admin.configuration.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Configuration
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Configure Default Weights</h6>
            </div>
            <div class="card-body">
                <p class="mb-4">
                    Set the default weightage percentages for performance evaluation components.
                    <strong>The total must sum to exactly 100%.</strong>
                </p>

                <form action="{{ route('admin.configuration.performance-weights-update') }}" method="POST" id="weightsForm">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="perf_weight_teaching" class="form-label">Teaching (%)</label>
                            <input type="number" class="form-control weight-input" id="perf_weight_teaching"
                                name="perf_weight_teaching" value="{{ old('perf_weight_teaching', $weights['teaching']) }}"
                                min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mt-4">Weightage for teaching activities and workload.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="perf_weight_research" class="form-label">Research (%)</label>
                            <input type="number" class="form-control weight-input" id="perf_weight_research"
                                name="perf_weight_research" value="{{ old('perf_weight_research', $weights['research']) }}"
                                min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mt-4">Weightage for research, publications, and grants.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="perf_weight_admin" class="form-label">Administrative (%)</label>
                            <input type="number" class="form-control weight-input" id="perf_weight_admin"
                                name="perf_weight_admin" value="{{ old('perf_weight_admin', $weights['admin']) }}" min="0"
                                max="100" required>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mt-4">Weightage for administrative duties and task
                                forces.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="perf_weight_support" class="form-label">Student Support (%)</label>
                            <input type="number" class="form-control weight-input" id="perf_weight_support"
                                name="perf_weight_support" value="{{ old('perf_weight_support', $weights['support']) }}"
                                min="0" max="100" required>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mt-4">Weightage for student advising and support
                                activities.</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body py-2 d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold">Total:</span>
                                    <span id="totalSum" class="font-weight-bold h5 mb-0">0%</span>
                                </div>
                            </div>
                            <div id="totalError" class="text-danger small mt-1" style="display:none;">Total must be exactly
                                100%</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save fa-sm text-white-50 mr-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.weight-input');
            const totalDisplay = document.getElementById('totalSum');
            const totalError = document.getElementById('totalError');
            const submitBtn = document.getElementById('submitBtn');

            function calculateTotal() {
                let total = 0;
                inputs.forEach(input => {
                    total += parseInt(input.value) || 0;
                });

                totalDisplay.textContent = total + '%';

                if (total === 100) {
                    totalDisplay.classList.remove('text-danger');
                    totalDisplay.classList.add('text-success');
                    totalError.style.display = 'none';
                    submitBtn.disabled = false;
                } else {
                    totalDisplay.classList.remove('text-success');
                    totalDisplay.classList.add('text-danger');
                    totalError.style.display = 'block';
                    // Optional: disable submit button until valid
                    // submitBtn.disabled = true; 
                }
            }

            inputs.forEach(input => {
                input.addEventListener('input', calculateTotal);
            });

            // Initial calculation
            calculateTotal();
        });
    </script>
@endsection