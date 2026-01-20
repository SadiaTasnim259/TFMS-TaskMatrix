@extends('admin.layouts.app')

@section('title', 'Review Departmental Submissions')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Review Departmental Submissions</h1>
                <p class="text-muted mb-0">
                    Process pending membership requests from departments.
                    @if(isset($currentSession))
                        <span class="badge bg-primary ms-2">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $currentSession->academic_year }} - Semester {{ $currentSession->semester }}
                        </span>
                    @endif
                </p>
            </div>
        </div>

        @if($pendingRequests->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Membership Requests</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Task Force</th>
                                    <th>Department / HOD</th>
                                    <th>Action</th>
                                    <th>Staff Member</th>
                                    <th>Role</th>
                                    <th>Submitted</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $request)
                                    <tr>
                                        <td class="ps-4 fw-bold">
                                            <a href="{{ route('psm.task-forces.show', $request->taskForce->id) }}"
                                                class="text-decoration-none">
                                                {{ $request->taskForce->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="small fw-bold">{{ $request->requester->department->name ?? 'N/A' }}</div>
                                            <div class="small text-muted">{{ $request->requester->name }}</div>
                                        </td>
                                        <td>
                                            @if($request->action === 'add')
                                                <span class="badge bg-success">Add Member</span>
                                            @else
                                                <span class="badge bg-danger">Remove Member</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2 bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    {{ substr($request->user->first_name ?? '?', 0, 1) }}{{ substr($request->user->last_name ?? '?', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $request->user->name }}</div>
                                                    <div class="small text-muted">{{ $request->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ ucfirst($request->role) }}</span>
                                        </td>
                                        <td class="small text-muted">
                                            {{ $request->created_at->diffForHumans() }}
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('psm.task-forces.show', $request->taskForce->id) }}#pending-requests"
                                                class="btn btn-sm btn-primary shadow-sm">
                                                <i class="fas fa-eye me-1"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-check-circle fa-4x text-success opacity-25"></i>
                </div>
                <h5 class="text-muted">No Pending Submissions</h5>
                <p class="text-muted mb-0">All departmental requests have been processed.</p>
            </div>
        @endif
    </div>
@endsection