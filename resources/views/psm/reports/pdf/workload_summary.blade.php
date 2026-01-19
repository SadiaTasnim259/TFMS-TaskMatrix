<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Workload Summary Report</title>
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

        .dept-header {
            background-color: #4a5568;
            color: #fff;
            padding: 8px 10px;
            margin-top: 20px;
            font-weight: bold;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .status-balanced {
            color: #22c55e;
        }

        .status-underloaded {
            color: #f59e0b;
        }

        .status-overloaded {
            color: #ef4444;
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
        <h1>Faculty Workload Summary</h1>
        <p>{{ config('app.name') }} - Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    @foreach($data as $dept)
        <div class="dept-header">{{ $dept->name }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Staff Name</th>
                    <th style="width: 15%">Staff ID</th>
                    <th style="width: 30%">Email</th>
                    <th style="width: 10%">Workload</th>
                    <th style="width: 10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dept->staff as $index => $staff)
                    @php
                        $workload = $staff->task_forces_sum_default_weightage ?? 0;
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
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->staff_id ?? 'N/A' }}</td>
                        <td>{{ $staff->email }}</td>
                        <td><strong>{{ $workload }}</strong></td>
                        <td class="{{ $statusClass }}">{{ $status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">No staff in this department.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>