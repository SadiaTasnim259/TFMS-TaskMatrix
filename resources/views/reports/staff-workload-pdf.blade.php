<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff Workload Report - {{ $staff->fullName() }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-section td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .info-section td:first-child {
            font-weight: bold;
            width: 30%;
            background: #f8f9fa;
        }
        .submissions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .submissions-table th {
            background: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .submissions-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .submissions-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-submitted {
            background: #fff3cd;
            color: #856404;
        }
        .status-draft {
            background: #e2e3e5;
            color: #383d41;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            margin-top: 0;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Staff Workload Report</h1>
        <p><strong>Task Force Management System (TFMS)</strong></p>
        <p>Generated on {{ now()->format('F d, Y H:i') }}</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td>Staff Name</td>
                <td>{{ $staff->fullName() }}</td>
            </tr>
            <tr>
                <td>Staff ID</td>
                <td>{{ $staff->staff_id }}</td>
            </tr>
            <tr>
                <td>Department</td>
                <td>{{ $staff->department->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Academic Year</td>
                <td>{{ $report->academic_year }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>{{ $report->semester == 'annual' ? 'Annual' : 'Semester ' . $report->semester }}</td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <h3>Summary</h3>
        <p><strong>Total Submissions:</strong> {{ $submissions->count() }}</p>
        <p><strong>Total Hours:</strong> {{ number_format($submissions->sum('total_hours'), 2) }}</p>
        <p><strong>Total Credits:</strong> {{ number_format($submissions->sum('total_credits'), 2) }}</p>
        <p><strong>Approved Submissions:</strong> {{ $submissions->where('status', 'approved')->count() }}</p>
    </div>

    @if($submissions->count() > 0)
        <h3>Workload Submissions</h3>
        <table class="submissions-table">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Semester</th>
                    <th>Hours</th>
                    <th>Credits</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $submission)
                <tr>
                    <td>{{ $submission->academic_year }}</td>
                    <td>{{ $submission->semester }}</td>
                    <td>{{ number_format($submission->total_hours, 2) }}</td>
                    <td>{{ number_format($submission->total_credits, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $submission->status }}">
                            {{ ucfirst($submission->status) }}
                        </span>
                    </td>
                    <td>{{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d') : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 20px; color: #666;">No workload submissions found for the selected period.</p>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the Task Force Management System.</p>
        <p>&copy; {{ date('Y') }} Task Force Management System. All rights reserved.</p>
    </div>
</body>
</html>
