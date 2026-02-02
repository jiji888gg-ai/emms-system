@extends('admin.layout')

@section('content')
<h4>Student Merit Detail</h4>

<div class="mb-3">
    <strong>Name:</strong> {{ $student->name }} <br>
    <strong>Matric No:</strong> {{ $student->num_matrics }} <br>
    <strong>Total Merit:</strong>
    <span class="badge bg-success">
        {{ $logs->sum('points_added') }}
    </span>

</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No.</th>
            <th>Event Name</th>
            <th>Merit Added</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($logs as $i => $log)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $log->event_title }}</td>
            <td>{{ $log->points_added }}</td>
            <td>{{ $log->created_at }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center text-muted">
                No merit record
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<a href="/admin/merit" class="btn btn-secondary">
    Back
</a>
@endsection
