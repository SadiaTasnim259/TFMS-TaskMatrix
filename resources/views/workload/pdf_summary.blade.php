<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Workload Summary - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px 0;
        }

        .label {
            font-weight: bold;
            width: 150px;
        }

        .status-box {
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            text-align: center;
            margin-bottom: 30px;
        }

        .status-title {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            letter-spacing: 1px;
        }

        .status-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }

        .workload-value {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th {
            background-color: #f1f1f1;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }

        table.data-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .footer {
            margin-top: 50px;
            font-size: 10px;
            text-align: center;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Workload Summary</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">Staff Name:</td>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="label">Staff ID:</td>
                <td>{{ $user->staff_id }}</td>
            </tr>
            <tr>
                <td class="label">Department:</td>
                <td>{{ $user->department->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Academic Session:</td>
                <td>
                    @if(isset($currentSession))
                        {{ $currentSession->academic_year }} - Semester {{ $currentSession->semester }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Date Generated:</td>
                <td>{{ now()->format('d M Y, h:i A') }}</td>
            </tr>
        </table>
    </div>

    <div class="status-box">
        <div class="status-title">Current Workload Status</div>
        <div class="status-value"
            style="color: {{ str_contains(strtolower($status), 'balanced') ? 'green' : (str_contains(strtolower($status), 'under') ? 'orange' : 'red') }}">
            {{ $status }}
        </div>
        <div>
            Total Weightage: <span class="workload-value">{{ $totalWorkload }}</span>
        </div>
        <div style="font-size: 12px; margin-top: 10px; color: #666;">
            Target Range: {{ $minWeightage }} - {{ $maxWeightage }}
        </div>
    </div>

    <h3>Assigned TaskForce</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Task Force Name</th>
                <th>Role</th>
                <th style="text-align: right;">Weightage</th>
            </tr>
        </thead>
        <tbody>
            @forelse($taskForces as $tf)
                <tr>
                    <td>{{ $tf->name }}</td>
                    <td>{{ $tf->pivot->role ?? 'Member' }}</td>
                    <td style="text-align: right;">{{ $tf->default_weightage }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px;">No active TaskForce found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This document is generated for appraisal purposes. Please contact administration for any discrepancies.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>