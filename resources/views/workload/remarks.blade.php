@extends('layouts.app')

@section('title', 'Submit Workload Remarks')

@section('content')
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Submit Workload Remarks</h1>
            <p class="text-muted small mb-0">Send remarks or concerns directly to your Head of Department.</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('workload.remarks.submit') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-bold">Your Remarks</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks"
                                name="remarks" rows="6"
                                placeholder="Type your remarks here... (e.g., Request for workload adjustment, specific concerns about task force distribution, etc.)"
                                required>{{ old('remarks') }}</textarea>

                            @error('remarks')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle me-1"></i> These remarks will be sent via email to your Head of
                                Department for review.
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-light me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane me-2"></i> Submit to HOD
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection