@extends('layouts.app')

@section('title', 'Reset Password - TFMS')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark">Set New Password</h2>
                        <p class="text-muted">Enter your new password below</p>
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

                                            <form method="POST" action="{{ route('password.update') }}">
                                                @csrf

                                                <input type="hidden" name="token" value="{{ $token }}">

                                                <!-- Email Field -->
                                                <div class="mb-3">
                                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-envelope text-muted"></i>
                                                        </span>
                                                        <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}"
                                                            class="form-control border-start-0 ps-0" placeholder="name@utm.my" required
                                                            autofocus>
                                                    </div>
                                                </div>

                                                <!-- Password Field -->
                                                <div class="mb-3">
                                                    <label for="password" class="form-label fw-semibold">New Password</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-lock text-muted"></i>
                                                        </span>
                                                        <input type="password" id="password" name="password"
                                                            class="form-control border-start-0 ps-0" placeholder="Enter new password" required>
                                                    </div>
                                                    <div class="form-text">Must be 8-16 characters long.</div>
                                                </div>

                                                <!-- Confirm Password Field -->
                                                <div class="mb-4">
                                                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-lock text-muted"></i>
                                                        </span>
                                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                                            class="form-control border-start-0 ps-0" placeholder="Confirm new password"
                                                            required>
                                                    </div>
                                                </div>

                                                <!-- Reset Button -->
                                                <div class="d-grid mb-4">
                                                    <button type="submit" class="btn text-white py-2 fw-bold"
                                                        style="background-color: var(--utm-maroon);">
                                                        Reset Password
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endsection