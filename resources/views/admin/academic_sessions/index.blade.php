@extends('layouts.app')

@section('title', 'Manage Academic Sessions')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Academic Sessions</h1>
            <a href="{{ route('admin.academic-sessions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Session
            </a>
        </div>



        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Sessions</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="sessionsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Duration</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td class="align-middle fw-bold">{{ $session->academic_year }}</td>
                                    <td class="align-middle">Semester {{ $session->semester }}</td>
                                    <td class="align-middle">
                                        {{ $session->start_date->format('d M Y') }} - {{ $session->end_date->format('d M Y') }}
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($session->is_active)
                                            <span class="badge bg-primary px-3 py-2">
                                                <i class="fas fa-check me-1"></i> CURRENT
                                            </span>
                                        @else
                                            <form action="{{ route('admin.academic-sessions.activate', $session) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    onclick="return confirm('Set this session as Active? This will deactivate the current session.')">
                                                    Set Active
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.academic-sessions.edit', $session) }}"
                                                class="btn btn-sm btn-info" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!$session->is_active)
                                                <form action="{{ route('admin.academic-sessions.destroy', $session) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger input-group-text"
                                                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;"
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this session?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled
                                                    title="Cannot delete active session">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No academic sessions found. Create one
                                        to get started.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection