<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SRMS Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>School Records Management System Report</h2>

    <h4>Users</h4>
    <table>
        <thead>
            <tr>
                <th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ ucfirst($u->role) }}</td>
                <td>{{ $u->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Documents by Category</h4>
    @php $groupedDocs = $documents->groupBy('category'); @endphp
    @forelse($groupedDocs as $category => $docs)
        <h5>{{ $category }}</h5>
        <table>
            <thead>
                <tr><th>Title</th><th>Uploader</th><th>Role</th><th>Created At</th></tr>
            </thead>
            <tbody>
                @foreach($docs as $doc)
                <tr>
                    <td>{{ $doc->title ?? $doc->file_name ?? 'Untitled' }}</td>
                    <td>{{ $doc->uploader ?? 'Unknown' }}</td>
                    <td>{{ $doc->uploader_role ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('F d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p>No documents match the filters.</p>
    @endforelse
</body>
</html>
