<!DOCTYPE html>
<html>

<head>
    <title>Overload/Underload Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-danger {
            color: #d63384;
            font-weight: bold;
        }

        /* Overload color */
        .text-warning {
            color: #fd7e14;
            font-weight: bold;
        }

        /* Underload color */
    </style>
</head>

<body>
    <h1>Workload Imbalance Report (Overload / Underload)</h1>
    <p>This report highlights staff members with workload outside the standard range (< 15 or> 40).</p>

    <table>
        <thead>
            <tr>
                <th>Staff Name</th>
                <th>Department</th>
                <th>Workload</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->department->name ?? 'N/A' }}</td>
                    <td>{{ $user->load_val }}</td>
                    <td>
                        @if($user->load_val > 40)
                            <span class="text-danger">Overload</span>
                        @elseif($user->load_val < 15)
                            <span class="text-warning">Underload</span>
                        @else
                            Normal
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>