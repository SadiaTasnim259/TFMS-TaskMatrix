<!DOCTYPE html>
<html>

<head>
    <title>Workload Summary Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
        }

        .dept-header {
            background-color: #333;
            color: #fff;
            padding: 5px;
            margin-top: 15px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
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
    </style>
</head>

<body>
    <h1>Faculty Workload Summary</h1>
    <div style="color: #666; font-size: 10px; margin-bottom: 20px;">Generated: {{ now()->toDateTimeString() }}</div>

    @foreach($data as $dept)
        <div class="dept-header">{{ $dept->name }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%">Staff Name</th>
                    <th style="width: 40%">Email</th>
                    <th style="width: 20%">Total Workload</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dept->staff as $staff)
                    <tr>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>
                            <strong>{{ $staff->calculateTotalWorkload() }}</strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>

</html>