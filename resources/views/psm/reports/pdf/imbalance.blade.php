<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Overload/Underload Report</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4a5568;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-danger {
            color: #ef4444;
            font-weight: bold;
        }

        .text-warning {
            color: #f59e0b;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Workload Imbalance Report</h1>
        <p>{{ config('app.name') }} - Generated on {{ now()->format('d M Y, h:i A') }}</p>
        <p>Staff members with workload outside the balanced range</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Staff Name</th>
                <th>Staff ID</th>
                <th>Department</th>
                <th>Workload</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $user)
                @php
                    $workload = $user->task_forces_sum_default_weightage ?? 0;
                    $status = $workloadService->calculateStatus($workload);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->staff_id ?? 'N/A' }}</td>
                    <td>{{ $user->department->name ?? 'N/A' }}</td>
                    <td>{{ $workload }}</td>
                    <td>
                        @if($status === 'Overloaded')
                            <span class="text-danger">Overloaded</span>
                        @elseif($status === 'Under-loaded')
                            <span class="text-warning">Under-loaded</span>
                        @else
                            {{ $status }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No imbalanced staff found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>