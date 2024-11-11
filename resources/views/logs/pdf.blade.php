<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
    </style>
</head>
<body>
<h2>Logs for File: {{ $file->name }}</h2>
<h3>groupName: {{ $groupName}}</h3>
<p>Generated on: {{ \Carbon\Carbon::now()->toDateTimeString() }}</p>

<table>
    <thead>
    <tr>
        <th>User Name</th>
        <th>Email</th>
        <th>Checked Out At</th>
        <th>Checked In At</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($file->actions as $action)
        <tr>
            <td>{{ $action->user->name }}</td>
            <td>{{ $action->user->email }}</td>
            <td>{{ $action->checked_out_at }}</td>
            <td>{{ $action->checked_in_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
