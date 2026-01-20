<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - TFMS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Additional CSS -->
    @yield('css')

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

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
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

        .sidebar {
            background-color: rgba(255, 255, 255, 0.94);
            min-height: calc(100vh - 64px);
            border-right: 1px solid rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(4px);
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: #334155;
            border-radius: 10px;
            margin: 6px 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
        }

        .sidebar .nav-link.active {
            background-color: rgba(92, 0, 31, 0.08);
            color: var(--utm-maroon);
            border: 1px solid rgba(92, 0, 31, 0.15);
        }

        .sidebar .nav-link:hover {
            background-color: rgba(248, 190, 23, 0.14);
            color: var(--utm-maroon);
        }

        .main-content {
            padding: 30px;
        }

        .card {
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 14px;
            box-shadow: 0 18px 45px -30px rgba(92, 0, 31, 0.35);
            margin-bottom: 20px;
        }

        .section-title {
            color: var(--utm-maroon);
            font-weight: 700;
        }

        .badge-soft {
            background: rgba(248, 190, 23, 0.15);
            color: var(--utm-maroon);
            border: 1px solid rgba(248, 190, 23, 0.35);
        }

        .hover-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .hover-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -20px rgba(92, 0, 31, 0.45) !important;
        }

        .dropdown-toggle.no-arrow::after {
            display: none;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <span class="brand-mark">TF</span>
                <div>
                    <strong>TFMS Portal</strong>
                    <div style="font-size: 12px; color: #f8be17;">UTM Faculty of AI</div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            Welcome, {{ auth()->user()->name }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <nav class="nav flex-column">
                    <a class="nav-link @if(request()->routeIs('dashboard')) active @endif"
                        href="{{ route('dashboard') }}">
                        📊 Dashboard
                    </a>

                    @if(auth()->user()->isAdmin())
                        <div class="nav-header text-uppercase text-muted small fw-bold mt-3 mb-1 px-3">Administration</div>

                        <a class="nav-link @if(request()->routeIs('admin.departments.*')) active @endif"
                            href="{{ route('admin.departments.index') }}">
                            🏢 Departments
                        </a>
                        <a class="nav-link @if(request()->routeIs('admin.task-forces.*')) active @endif"
                            href="{{ route('admin.task-forces.index') }}">
                            📋 Taskforce Master Data
                        </a>
                        <a class="nav-link @if(request()->routeIs('admin.academic-sessions.*')) active @endif"
                            href="{{ route('admin.academic-sessions.index') }}">
                            🗓️ Academic Session / Semester
                        </a>
                        <a class="nav-link @if(request()->routeIs('admin.workload.thresholds.edit')) active @endif"
                            href="{{ route('admin.workload.thresholds.edit') }}">
                            ⚙️ Workload Threshold Range
                        </a>
                        <a class="nav-link @if(request()->routeIs('admin.users.*')) active @endif"
                            href="{{ route('admin.users.index') }}">
                            🔑 Staff Master Data
                        </a>
                        <a class="nav-link @if(request()->routeIs('admin.audit-logs.*')) active @endif"
                            href="{{ route('admin.audit-logs.index') }}">
                            📊 View Audit Log
                        </a>
                    @endif

                    @if(auth()->user()->isHOD())
                        <div class="nav-header text-uppercase text-muted small fw-bold mt-3 mb-1 px-3">Department</div>
                        <a class="nav-link @if(request()->routeIs('hod.workload.*')) active @endif"
                            href="{{ route('hod.workload.index') }}">
                            ⚖️ Workload
                        </a>
                        <a class="nav-link @if(request()->routeIs('hod.task-forces.*')) active @endif"
                            href="{{ route('hod.task-forces.index') }}">
                            📋 TaskForce
                        </a>
                    @endif

                    @if(auth()->user()->isPSM())
                        <div class="nav-header text-uppercase text-muted small fw-bold mt-3 mb-1 px-3">HR & PSM</div>
                        {{-- Hidden for now
                        <a class="nav-link @if(request()->routeIs('psm.workload.index')) active @endif"
                            href="{{ route('psm.workload.index') }}">
                            🏢 Faculty Overview
                        </a>
                        <a class="nav-link @if(request()->routeIs('psm.workload.imbalance')) active @endif"
                            href="{{ route('psm.workload.imbalance') }}">
                            ⚖️ Imbalance Watch
                        </a>
                        --}}
                        <a class="nav-link @if(request()->routeIs('psm.reports.index')) active @endif"
                            href="{{ route('psm.reports.index') }}">
                            📊 Generate Faculty Reports
                        </a>
                        <a class="nav-link @if(request()->routeIs('psm.task-forces.*') && !request()->routeIs('psm.task-forces.requests')) active @endif"
                            href="{{ route('psm.task-forces.index') }}">
                            📋 View Faculty Taskforces
                        </a>
                        <a class="nav-link @if(request()->routeIs('psm.task-forces.requests')) active @endif"
                            href="{{ route('psm.task-forces.requests') }}">
                            📨 Review Dept. Submissions
                        </a>
                    @endif

                    @if(auth()->user()->isLecturer())
                        <div class="nav-header text-uppercase text-muted small fw-bold mt-3 mb-1 px-3">My Portfolio</div>
                        <a class="nav-link @if(request()->routeIs('workload.assigned-task-forces')) active @endif"
                            href="{{ route('workload.assigned-task-forces') }}">
                            💼 Assigned Taskforces
                        </a>
                        <a class="nav-link @if(request()->routeIs('workload.summary')) active @endif"
                            href="{{ route('workload.summary') }}">
                            📈 View Workload Summary
                        </a>
                        <a class="nav-link @if(request()->routeIs('workload.remarks')) active @endif"
                            href="{{ route('workload.remarks') }}">
                            💬 Submit Workload Remarks
                        </a>
                        <a class="nav-link @if(request()->routeIs('workload.history')) active @endif"
                            href="{{ route('workload.history') }}">
                            📜 View Historical Records
                        </a>
                        <a class="nav-link" href="{{ route('workload.summary.download') }}" target="_blank">
                            🖨️ Download / Print Summary
                        </a>
                    @endif

                    @if(auth()->user()->hasRole('management'))
                        <div class="nav-header text-uppercase text-muted small fw-bold mt-3 mb-1 px-3">Executive</div>
                        <a class="nav-link @if(request()->routeIs('management.dashboard')) active @endif"
                            href="{{ route('management.dashboard') }}">
                            📈 Workload Overview
                        </a>
                        <a class="nav-link @if(request()->routeIs('management.task_distribution')) active @endif"
                            href="{{ route('management.task_distribution') }}">
                            📊 Taskforce Distribution
                        </a>
                        <a class="nav-link @if(request()->routeIs('management.department_comparison')) active @endif"
                            href="{{ route('management.department_comparison') }}">
                            🏢 Department Comparison
                        </a>
                        <a class="nav-link @if(request()->routeIs('management.export_reports')) active @endif"
                            href="{{ route('management.export_reports') }}">
                            📥 Export Summary Reports
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Validation Errors:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Additional JS -->
    @yield('js')
</body>

</html>