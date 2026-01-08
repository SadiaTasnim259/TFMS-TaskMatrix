<!DOCTYPE html>
<html>
<head>
    <title>Management Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2 { color: #333; }
        .stats-grid { display: table; width: 100%; margin-bottom: 20px; }
        .stat-card { display: table-cell; padding: 10px; border: 1px solid #ddd; text-align: center; }
    </style>
</head>
<body>
    <h1>Executive Management Report</h1>
    <p>Generated on: {{ now()->format('d M Y, H:i') }}</p>

    <h2>Workload Fairness Overview</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Under-loaded</h3>
            <p>{{ $fairnessStats['Under-loaded'] }}</p>
        </div>
        <div class="stat-card">
            <h3>Balanced</h3>
            <p>{{ $fairnessStats['Balanced'] }}</p>
        </div>
        <div class="stat-card">
            <h3>Overloaded</h3>
            <p>{{ $fairnessStats['Overloaded'] }}</p>
        </div>
    </div>

    <h2>Task Force Distribution</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Total Task Forces</th>
            </tr>
        </thead>
        <tbody>
            @foreach($taskForceByCategory as $category => $count)
            <tr>
                <td>{{ ucfirst($category) }}</td>
                <td>{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Department Comparison</h2>
    <table>
        <thead>
            <tr>
                <th>Department</th>
                <th>Avg Weightage</th>
                <th>Staff Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departmentStats as $stat)
            <tr>
                <td>{{ $stat['name'] }}</td>
                <td>{{ $stat['average_weightage'] }}</td>
                <td>{{ $stat['staff_count'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
