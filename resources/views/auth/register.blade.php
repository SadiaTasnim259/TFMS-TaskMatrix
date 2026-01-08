@extends('layouts.app')

@section('title', 'Register - TFMS')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark">Create Account</h2>
                        <p class="text-muted">Join TFMS today</p>
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

                                            <form method="POST" action="{{ route('register.post') }}">
                                                @csrf

                                                <!-- Name Field -->
                                                <div class="mb-3">
                                                    <label for="name" class="form-label fw-semibold">Full Name</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-user text-muted"></i>
                                                        </span>
                                                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                                                            class="form-control border-start-0 ps-0" placeholder="Enter your full name" required
                                                            autofocus>
                                                    </div>
                                                </div>

                                                <!-- Email Field -->
                                                <div class="mb-3">
                                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-envelope text-muted"></i>
                                                        </span>
                                                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                                                            class="form-control border-start-0 ps-0" placeholder="name@utm.my" required>
                                                    </div>
                                                </div>

                                                <!-- Password Field -->
                                                <div class="mb-3">
                                                    <label for="password" class="form-label fw-semibold">Password</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-lock text-muted"></i>
                                                        </span>
                                                        <input type="password" id="password" name="password"
                                                            class="form-control border-start-0 ps-0" placeholder="Create a password" required>
                                                    </div>
                                                </div>

                                                <!-- Confirm Password Field -->
                                                <div class="mb-4">
                                                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-lock text-muted"></i>
                                                        </span>
                                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                                            class="form-control border-start-0 ps-0" placeholder="Confirm your password"
                                                            required>
                                                    </div>
                                                </div>

                                                <!-- Register Button -->
                                                <div class="d-grid mb-4">
                                                    <button type="submit" class="btn text-white py-2 fw-bold"
                                                        style="background-color: var(--utm-maroon);">
                                                        Create Account
                                                    </button>
                                                </div>

                                                <!-- Login Link -->
                                                <div class="text-center">
                                                    <p class="text-muted mb-0">
                                                        Already have an account?
                                                        <a href="{{ route('login') }}" class="text-decoration-none fw-bold"
                                                            style="color: var(--utm-maroon);">
                                                            Sign in here
                                                        </a>
                                                    </p>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endsection