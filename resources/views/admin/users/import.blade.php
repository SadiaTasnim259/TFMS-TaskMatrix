@extends('admin.layouts.app')

@section('title', 'Import Users - TFMS')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 section-title mb-0">Import Users</h1>
            <p class="text-muted mb-0">Bulk import users/staff from Excel or CSV.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            &larr; Back to Users
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Instructions</h5>
                <p class="mb-0">
                    The file must contain the following columns: <code>StaffID</code>, <code>FirstName</code>,
                    <code>LastName</code>, <code>Email</code>, <code>Designation</code>, <code>DepartmentCode</code>.
                    <br>
                    Passwords will be auto-generated and optional flags (HOD, Coordinator) can be set via columns
                    <code>IsHOD</code>, etc.
                </p>
            </div>

            <form action="{{ route('admin.users.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="file" class="form-label">Select File (CSV, Excel)</label>
                    <input type="file" name="file" id="file" class="form-control" required accept=".csv, .xls, .xlsx">
                    @error('file')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-upload me-2"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection