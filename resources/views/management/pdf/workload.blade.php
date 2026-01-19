<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Workload Summary Report</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #4a5568;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .status-balanced {
            color: #22c55e;
            font-weight: bold;
        }

        .status-underloaded {
            color: #f59e0b;
            font-weight: bold;
        }

        .status-overloaded {
            color: #ef4444;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .summary {
            background-color: #f1f5f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .summary p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Workload Summary Report</h1>
        <p>{{ config('app.name') }} - Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="summary">
        <p><strong>Total Staff:</strong> {{ $users->count() }}</p>
        <p><strong>Report Type:</strong> All Staff Workload Summary</p>
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
            @foreach($users as $index => $user)
                @php
                    $workload = $user->task_forces_sum_default_weightage ?? 0;
                    $status = $workloadService->calculateStatus($workload);
                    $statusClass = match ($status) {
                        'Balanced' => 'status-balanced',
                        'Under-loaded' => 'status-underloaded',
                        'Overloaded' => 'status-overloaded',
                        default => ''
                    };
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->staff_id ?? 'N/A' }}</td>
                    <td>{{ $user->department->name ?? 'N/A' }}</td>
                    <td>{{ $workload }}</td>
                    <td class="{{ $statusClass }}">{{ $status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>