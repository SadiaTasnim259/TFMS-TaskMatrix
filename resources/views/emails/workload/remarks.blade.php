<!DOCTYPE html>
<html>

<head>
    <title>Workload Remarks Submitted</title>
</head>

<body>
    <h1>Workload Remarks Submitted</h1>
    <p><strong>Lecturer:</strong> {{ $lecturer->name }} ({{ $lecturer->email }})</p>
    <p><strong>Department:</strong> {{ $lecturer->department->name ?? 'N/A' }}</p>

    <p><strong>Remarks:</strong></p>
    <p style="white-space: pre-wrap;">{{ $remarks }}</p>

    <p>
        <a href="{{ route('login') }}"
            style="background-color: #4e73df; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Login
            to TFMS</a>
    </p>

    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>

</html>