<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - TFMS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --utm-maroon: #5c001f;
            --utm-gold: #f8be17;
            --utm-sand: #ffffab;
            --ink: #0f172a;
        }

        body {
            background:
                radial-gradient(circle at 10% 10%, rgba(248, 190, 23, 0.12), transparent 25%),
                radial-gradient(circle at 85% 15%, rgba(92, 0, 31, 0.1), transparent 22%),
                #fffdf2;
            min-height: 100vh;
            color: var(--ink);
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .navbar {
            box-shadow: 0 10px 30px -18px rgba(15, 23, 42, 0.5);
            background: linear-gradient(120deg, rgba(92, 0, 31, 0.97), rgba(92, 0, 31, 0.9));
        }

        .brand-mark {
            height: 36px;
            width: 36px;
            border-radius: 12px;
            background: var(--utm-gold);
            color: var(--utm-maroon);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.4);
        }

        .card {
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 14px;
            box-shadow: 0 18px 45px -30px rgba(92, 0, 31, 0.35);
        }

        .btn-maroon {
            background-color: var(--utm-maroon);
            color: white;
        }

        .btn-maroon:hover {
            background-color: #3d0014;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Simple Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid justify-content-center">
            <span class="navbar-brand mb-0 h1 d-flex align-items-center gap-2">
                <span class="brand-mark">TF</span>
                <span class="d-flex flex-column lh-1 text-start">
                    <span class="fs-6 fw-bold">TFMS Portal</span>
                    <span style="font-size: 10px; color: var(--utm-gold);">UTM Faculty of AI</span>
                </span>
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                            <h2 class="fw-bold text-dark">Change Password</h2>
                            <p class="text-muted">For your security, you must update your password before proceeding.
                            </p>
                        </div>

                        <!-- Flash Messages -->
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $message }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0 ps-3 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('change-password.post') }}">
                            @csrf

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">Current Password</label>
                                <input type="password" id="current_password" name="current_password"
                                    class="form-control" placeholder="Enter current password" required autofocus>
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">New Password</label>
                                <input type="password" id="password" name="password" class="form-control"
                                    placeholder="Min 8 chars, 1 letter, 1 number" required>
                                <div class="form-text small">Must be 8-16 characters with letters and numbers.</div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirm New
                                    Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control" placeholder="Re-enter new password" required>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-maroon py-2 fw-bold">
                                    <i class="fas fa-save me-2"></i> Update Password and Login
                                </button>

                                <a class="btn btn-outline-secondary btn-sm mt-2" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Cancel & Logout
                                </a>
                            </div>
                        </form>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>