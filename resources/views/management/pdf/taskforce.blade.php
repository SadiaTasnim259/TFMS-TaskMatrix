<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Taskforce Distribution Report</title>
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

        .text-center {
            text-align: center;
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
        <h1>Taskforce Distribution Report</h1>
        <p>{{ config('app.name') }} - Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="summary">
        <p><strong>Total Departments:</strong> {{ $departments->count() }}</p>
        <p><strong>Total Active TaskForce:</strong> {{ $departments->sum('task_forces_count') }}</p>
        <p><strong>Report Type:</strong> Task Force Distribution by Department</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Department</th>
                <th class="text-center">Active TaskForce</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departments as $index => $dept)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $dept->name }}</td>
                    <td class="text-center">{{ $dept->task_forces_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>