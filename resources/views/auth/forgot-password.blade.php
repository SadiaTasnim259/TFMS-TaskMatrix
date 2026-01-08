@extends('layouts.app')

@section('title', 'Forgot Password - TFMS')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark">Reset Password</h2>
                        <p class="text-muted">Enter your email to receive a reset link</p>
                    </div>

                    @section('hide_global_errors', true)

                                            @if ($errors->any())
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <ul class="mb-0 ps-3">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            @endif

                                            @if (session('status'))
                                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                    {{ session('status') }}
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            @endif

                                            <form method="POST" action="{{ route('password.email') }}">
                                                @csrf

                                                <!-- Email Field -->
                                                <div class="mb-4">
                                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-envelope text-muted"></i>
                                                        </span>
                                                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                                                            class="form-control border-start-0 ps-0" placeholder="name@utm.my" required
                                                            autofocus>
                                                    </div>
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="d-grid mb-4">
                                                    <button type="submit" class="btn text-white py-2 fw-bold"
                                                        style="background-color: var(--utm-maroon);">
                                                        Send Reset Link
                                                    </button>
                                                </div>

                                                <!-- Back to Login -->
                                                <div class="text-center">
                                                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold"
                                                        style="color: var(--utm-maroon);">
                                                        <i class="fas fa-arrow-left me-1"></i> Back to Login
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endsection