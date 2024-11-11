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
<h2>Logs for User: {{ $user->name }}</h2>
<p>Email: {{ $user->email }}</p>
<p>Generated on: {{ \Carbon\Carbon::now()->toDateTimeString() }}</p>

<table>
    <thead>
    <tr>
        <th>File Name</th>
        <th>Checked Out At</th>
        <th>Checked In At</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($user->actions as $action)
        <tr>
            <td>{{ $action->file->name }}</td>
            <td>{{ $action->checked_out_at }}</td>
            <td>{{ $action->checked_in_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
