@extends('admin.layouts.app')

@section('title', 'Workload Threshold Range')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0"><i class="fas fa-sliders-h me-2"></i> Workload Threshold Range</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Set the minimum and maximum workload hours to determine the "Balanced" status range.
                        <br>
                        <small><strong>Note:</strong> Values must be between 0 and 20. Maximum must be greater than
                            Minimum.</small>
                    </p>



                    <form action="{{ route('admin.workload.thresholds.update') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <label for="min_weightage" class="form-label fw-bold text-secondary">Minimum
                                            Workload</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" min="0" max="20"
                                                class="form-control text-center fs-4 @error('min_weightage') is-invalid @enderror"
                                                id="min_weightage" name="min_weightage"
                                                value="{{ old('min_weightage', $thresholds['min']) }}" required>
                                        </div>
                                        @error('min_weightage')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-2">Below this is "Under-loaded"</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <label for="max_weightage" class="form-label fw-bold text-secondary">Maximum
                                            Workload</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" min="0" max="20"
                                                class="form-control text-center fs-4 @error('max_weightage') is-invalid @enderror"
                                                id="max_weightage" name="max_weightage"
                                                value="{{ old('max_weightage', $thresholds['max']) }}" required>
                                        </div>
                                        @error('max_weightage')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-2">Above this is "Overloaded"</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection